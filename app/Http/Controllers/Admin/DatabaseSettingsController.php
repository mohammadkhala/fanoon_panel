<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Doctrine\DBAL\DriverManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseSettingsController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function databaseIndex(): View|Factory|Application
    {
        $laravelConnection = DB::connection();

        // Create Doctrine DBAL connection with credentials
        $connection = DriverManager::getConnection([
            'dbname'   => $laravelConnection->getDatabaseName(),
            'user'     => $laravelConnection->getConfig('username'),
            'password' => $laravelConnection->getConfig('password'),
            'host'     => $laravelConnection->getConfig('host'),
            'port'     => $laravelConnection->getConfig('port'),
            'driver'   => 'pdo_mysql',
            'pdo'      => $laravelConnection->getPdo(), // reuse Laravel PDO
        ]);

        // Get tables from Doctrine
        $schemaManager = $connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        // Exclude unwanted tables
        $filterTables = [
            'admins', 'branches', 'business_settings', 'email_verifications', 'failed_jobs', 'migrations',
            'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients',
            'oauth_refresh_tokens', 'password_resets', 'phone_verifications',
            'users', 'currencies', 'colors'
        ];

        $tables = array_values(array_diff($tables, $filterTables));

        // Build one variable: [ ['name' => 'table', 'rows' => count], ... ]
        $tables = collect($tables)->map(function ($table) {
            return [
                'name' => $table,
                'rows' => DB::table($table)->count(), // exact row count
            ];
        });

        return view('admin-views.business-settings.db-index', compact('tables'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cleanDatabase(Request $request): RedirectResponse
    {
        $tables = (array)$request->tables;

        if(count($tables) == 0) {
            Toastr::error(translate('No Table Updated'));
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error(translate('Failed to update!'));
            return back();
        }

        Toastr::success(translate('Updated successfully!'));
        return back();
    }

    /**
     * Download a full SQL dump of the current database.
     *
     * @return StreamedResponse
     */
    public function download(): StreamedResponse
    {
        $connection = DB::connection();
        $database = $connection->getDatabaseName();
        $key = 'Tables_in_' . $database;

        $callback = function () use ($connection, $database, $key) {
            echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = $connection->select('SHOW TABLES');

            foreach ($tables as $tableObj) {
                if (!isset($tableObj->$key)) {
                    continue;
                }
                $table = $tableObj->$key;

                $create = $connection->selectOne("SHOW CREATE TABLE `$table`");
                if (!$create || !isset($create->{'Create Table'})) {
                    continue;
                }

                $createSql = $create->{'Create Table'};
                echo "DROP TABLE IF EXISTS `$table`;\n";
                echo $createSql . ";\n\n";

                $rows = $connection->table($table)->get();
                if ($rows->isEmpty()) {
                    echo "\n";
                    continue;
                }

                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = array_map(static function ($column) {
                        return "`{$column}`";
                    }, array_keys($rowArray));

                    $values = array_map(static function ($value) use ($connection) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return $connection->getPdo()->quote($value);
                    }, array_values($rowArray));

                    echo "INSERT INTO `$table` (" . implode(',', $columns) . ") VALUES (" . implode(',', $values) . ");\n";
                }

                echo "\n\n";
                if (function_exists('flush')) {
                    flush();
                }
            }

            echo "SET FOREIGN_KEY_CHECKS=1;\n";
        };

        $filename = 'database_backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }
}
