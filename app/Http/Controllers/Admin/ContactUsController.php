<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function __construct(
        private ContactUs $contactUs
    ) {
    }

    /**
     * List contact us messages with filter (all / unread / read).
     */
    public function index(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $filter = $request->query('filter', 'all'); // all, unread, read

        $query = $this->contactUs->newQuery()->latest();

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $messages = $query->paginate($perPage)->withQueryString();
        $unreadCount = $this->contactUs->unread()->count();

        return view('admin-views.contact-us.list', compact('messages', 'filter', 'perPage', 'unreadCount'));
    }

    /**
     * Show a single message and mark as read.
     */
    public function show(int $id): View|Factory|Application|RedirectResponse
    {
        $message = $this->contactUs->find($id);
        if (!$message) {
            Toastr::error(translate('Message not found'));
            return redirect()->route('admin.contact-us.index');
        }
        if (is_null($message->read_at)) {
            $message->read_at = now();
            $message->save();
        }
        return view('admin-views.contact-us.show', compact('message'));
    }

    /**
     * Delete a contact us message.
     */
    public function destroy(int $id): RedirectResponse
    {
        $message = $this->contactUs->find($id);
        if (!$message) {
            Toastr::error(translate('Message not found'));
            return redirect()->route('admin.contact-us.index');
        }
        $message->delete();
        Toastr::success(translate('Contact message deleted successfully'));
        return redirect()->route('admin.contact-us.index');
    }
}
