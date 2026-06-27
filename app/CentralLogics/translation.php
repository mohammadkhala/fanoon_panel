<?php

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\App;

if(!function_exists('translate')) {
    function translate($key)
    {
        $local = session()->has('local') ? session('local') : 'ar';
        App::setLocale($local);

        $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
        $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));

        if (!array_key_exists($key, $lang_array)) {
            $lang_array[$key] = $processed_key;
            $path = base_path('resources/lang/' . $local . '/messages.php');
            $str = "<?php return " . var_export($lang_array, true) . ";";
            // الإنتاج: ملفات اللغة غالباً غير قابلة للكتابة من PHP-FPM — لا نُسقط الطلب بـ 500
            $dir = dirname($path);
            $canWrite = (file_exists($path) && is_writable($path))
                || (! file_exists($path) && is_writable($dir));
            if ($canWrite) {
                @file_put_contents($path, $str);
            }
            $result = $processed_key;
        } else {
            $result = __('messages.' . $key);
        }
        return $result;
    }
}
