<?php

namespace App\Observers;

use App\Models\ContactUs;
use Illuminate\Support\Facades\Cache;

class ContactUsObserver
{
    /**
     * رسائل تواصل معنا تؤثر على عدّاد new_contact_us في get-store-data (كاش 10 ث).
     */
    public function saved(ContactUs $contactUs): void
    {
        Cache::forget('admin_store_data');
    }

    public function deleted(ContactUs $contactUs): void
    {
        Cache::forget('admin_store_data');
    }
}
