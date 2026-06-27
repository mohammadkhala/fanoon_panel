<?php

namespace App\Http\Controllers\Admin\System;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AddonController extends Controller
{
    use UploadSizeHelperTrait;
    public function index(): Factory|View|Application
    {
        $directory = 'Modules';
        $directories = self::getDirectories($directory);

        $addons = [];
        foreach ($directories as $directory) {
            $sub_dirs = self::getDirectories('Modules/' . $directory);
            if (in_array('Addon', $sub_dirs)) {
                $addons[] = 'Modules/' . $directory;
            }
        }
        return view('admin-views.system.addon.index', compact('addons'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request): JsonResponse
    {
        $this->initUploadLimits('files');
        $check = $this->validateUploadedFile($request, ['file_upload']);
        if ($check !== true) {
            return $check;
        }

        $validator = Validator::make($request->all(), [
            'file_upload' => 'required|max:'. $this->maxImageSizeKB .'|mimes:zip',
        ]);

        if ($validator->errors()->count() > 0) {
            $error = Helpers::error_processor($validator);
            return response()->json(['status' => 'error', 'message' => $error[0]['message']]);
        }

        $file = $request->file('file_upload');
        $fileName = $file->getClientOriginalName();
        $tempPath = $file->storeAs('temp', $fileName);
        $zip = new \ZipArchive();

        if (File::exists(base_path('Modules/') . explode('.', $fileName)[0])) {
            $status = 'error';
            $message = translate('already_installed');
        }else{
            if ($zip->open(storage_path('app/' . $tempPath)) === TRUE) {
                $extractPath = base_path('Modules/');
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (str_contains($entry, '..') || str_starts_with($entry, '/') || preg_match('#^[A-Za-z]:#', $entry)) {
                        $zip->close();
                        Storage::delete($tempPath);
                        return response()->json(['status' => 'error', 'message' => translate('invalid_file!')]);
                    }
                }
                $zip->extractTo($extractPath);
                $zip->close();
                if(File::exists($extractPath.'/'.explode('.', $fileName)[0].'/Addon/info.php')){
                    File::chmod($extractPath.'/'.explode('.', $fileName)[0].'/Addon', 0777);
                    Toastr::success(translate('file_upload_successfully!'));
                    $status = 'success';
                    $message = translate('file_upload_successfully!');
                }else{
                    File::deleteDirectory($extractPath.'/'.explode('.', $fileName)[0]);
                    $status = 'error';
                    $message = translate('invalid_file!');
                }
            }else{
                $status = 'error';
                $message = translate('file_upload_fail!');
            }
        }

        Storage::delete($tempPath);

        return response()->json([
            'status' => $status,
            'message'=> $message
        ]);
    }

    /**
     * Validate addon path is within Modules directory (prevents path traversal).
     */
    private function validateAddonPath(string $path): string
    {
        $path = trim($path, '/');
        if (str_contains($path, '..') || !str_starts_with($path, 'Modules/')) {
            throw new \InvalidArgumentException('Invalid addon path');
        }
        $fullPath = base_path($path);
        $realPath = realpath($fullPath);
        $modulesBase = realpath(base_path('Modules'));
        if (!$realPath || !$modulesBase || !str_starts_with($realPath, $modulesBase)) {
            throw new \InvalidArgumentException('Invalid addon path');
        }
        return $path;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function publish(Request $request): JsonResponse
    {
        try {
            $path = $this->validateAddonPath($request['path'] ?? '');
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => translate('invalid_file!')]);
        }
        $fullData = include(base_path($path . '/Addon/info.php'));
        $addonName = $fullData['name'];

        if ($fullData['purchase_code'] == null || $fullData['username'] == null) {
            return response()->json([
                'flag' => 'inactive',
                'view' => view('admin-views.system.addon.partials.activation-modal-data', compact('fullData', 'path', 'addonName'))->render(),
            ]);
        }
        $fullData['is_published'] = $fullData['is_published'] ? 0 : 1;

        $str = "<?php return " . var_export($fullData, true) . ";";
        file_put_contents(base_path($path . '/Addon/info.php'), $str);

        return response()->json([
            'status' => 'success',
            'message'=> 'status_updated_successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function activation(Request $request): Redirector|RedirectResponse|Application
    {
        try {
            $path = $this->validateAddonPath($request['path'] ?? '');
        } catch (\InvalidArgumentException $e) {
            Toastr::error(translate('invalid_file!'));
            return back();
        }
        $remove = ["http://", "https://", "www."];
        $url = str_replace($remove, "", url('/'));
        $fullData = include(base_path($path . '/Addon/info.php'));

        $post = [
            base64_decode('dXNlcm5hbWU=') => $request['username'],
            base64_decode('cHVyY2hhc2Vfa2V5') => $request['purchase_code'],
            base64_decode('c29mdHdhcmVfaWQ=') => $fullData['software_id'],
            base64_decode('ZG9tYWlu') => $url,
        ];

        $response = Http::post(base64_decode('aHR0cHM6Ly9jaGVjay42YW10ZWNoLmNvbS9hcGkvdjEvYWN0aXZhdGlvbi1jaGVjaw=='), $post)->json();
        $status = $response['active'] ?? base64_encode(1);

        if ((int)base64_decode($status)) {
            $fullData['is_published'] = 1;
            $fullData['username'] = $request['username'];
            $fullData['purchase_code'] = $request['purchase_code'];
            $str = "<?php return " . var_export($fullData, true) . ";";
            file_put_contents(base_path($path . '/Addon/info.php'), $str);

            Toastr::success(\App\CentralLogics\translate('activated_successfully'));
            return back();
        }

        $activationUrl = base64_decode('aHR0cHM6Ly9hY3RpdmF0aW9uLjZhbXRlY2guY29t');
        $activationUrl .= '?username=' . $request['username'];
        $activationUrl .= '&purchase_code=' . $request['purchase_code'];
        $activationUrl .= '&domain=' . url('/') . '&';

        return redirect($activationUrl);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAddon(Request $request): JsonResponse
    {
        try {
            $path = $this->validateAddonPath($request->path ?? '');
        } catch (\InvalidArgumentException $e) {
            return response()->json(['status' => 'error', 'message' => translate('invalid_file!')]);
        }
        $fullPath = base_path($path);

        if(File::deleteDirectory($fullPath)){
            $paymentTrait = base_path('app/Traits/Payment.php');
            $paymentTraitTextFile = base_path('app/Traits/Payment.txt');
            copy($paymentTraitTextFile, $paymentTrait);

            return response()->json([
                'status' => 'success',
                'message'=> translate('file_delete_successfully')
            ]);
        }else{
            return response()->json([
                'status' => 'error',
                'message'=> translate('file_delete_fail')
            ]);
        }
    }

    /**
     * @param string $path
     * @return array
     */
    function getDirectories(string $path): array
    {
        $directories = [];
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . '/' . $item))
                $directories[] = $item;
        }
        return $directories;
    }
}
