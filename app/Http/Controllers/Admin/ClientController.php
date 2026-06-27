<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;

class ClientController extends Controller
{
    use UploadSizeHelperTrait;

    public function __construct(
        private Client $client
    ) {
        $this->initUploadLimits();
    }

    /**
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');
        $status = $request->query('status');
        $queryParams = ['per_page' => $perPage];

        $query = $this->client;

        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where('name', 'like', "%{$search}%");
        }

        if ($status !== null && $status !== '') {
            $queryParams['status'] = $status;
            $query = $query->where('status', (int) $status);
        }

        $clients = $query->orderBy('position')->orderBy('id', 'desc')->paginate($perPage)->appends($queryParams);

        return view('admin-views.client.index', compact('clients', 'search', 'perPage', 'status'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $check = $this->validateUploadedFile($request, ['logo']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'name' => 'required|max:255',
            'logo' => 'nullable|image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'link' => 'nullable|url|max:255',
        ], [
            'name.required' => 'Client name is required!',
            'logo.mimes' => 'Logo must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'logo.max' => translate('Logo size must be below ' . $this->maxImageSizeReadable),
        ]);

        $client = $this->client;
        $client->name = $request->name;
        $client->link = $request->link;
        $client->position = (int) $request->input('position', 0);
        $client->status = 1;

        if ($request->hasFile('logo')) {
            $client->logo = Helpers::upload('client/', APPLICATION_IMAGE_FORMAT, $request->file('logo'));
        }

        $client->save();
        Cache::forget(CACHE_CLIENT_TABLE);

        Toastr::success(translate('Client added successfully!'));
        return redirect()->route('admin.client.add-new');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $client = $this->client->find($id);
        return view('admin-views.client.edit', compact('client'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Request $request, $id): Redirector|RedirectResponse|Application
    {
        $check = $this->validateUploadedFile($request, ['logo']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'name' => 'required|max:255',
            'logo' => 'nullable|image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'link' => 'nullable|url|max:255',
        ], [
            'name.required' => 'Client name is required!',
            'logo.mimes' => 'Logo must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'logo.max' => translate('Logo size must be below ' . $this->maxImageSizeReadable),
        ]);

        $client = $this->client->find($id);
        $client->name = $request->name;
        $client->link = $request->link;
        $client->position = (int) $request->input('position', 0);

        if ($request->hasFile('logo')) {
            $client->logo = Helpers::update('client/', $client->logo, APPLICATION_IMAGE_FORMAT, $request->file('logo'));
        }

        $client->save();
        Cache::forget(CACHE_CLIENT_TABLE);

        Toastr::success(translate('Client updated successfully!'));
        return redirect()->route('admin.client.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $client = $this->client->find($request->id);
        $client->status = $request->status;
        $client->save();
        Cache::forget(CACHE_CLIENT_TABLE);

        Toastr::success(translate('Client status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $client = $this->client->find($request->id);
        if ($client->logo) {
            Helpers::delete('client/' . $client->logo);
        }
        $client->delete();
        Cache::forget(CACHE_CLIENT_TABLE);

        Toastr::success(translate('Client removed!'));
        return back();
    }
}
