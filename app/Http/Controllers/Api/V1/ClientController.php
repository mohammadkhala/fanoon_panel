<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ClientController extends Controller
{
    public function __construct(
        private Client $client
    ) {
    }

    /**
     * @return JsonResponse
     */
    public function getClients(): JsonResponse
    {
        $clients = Cache::rememberForever(CACHE_CLIENT_TABLE, function () {
            return $this->client->active()->orderBy('position')->orderBy('id', 'desc')->get();
        });

        return response()->json($clients, 200);
    }
}
