<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\BusinessSetting;
use App\Models\EmailVerifications;
use App\Models\PhoneVerification;
use App\Models\LoginSetup;
use App\Models\User;
use App\Models\UserType;
use App\Services\WaSenderService;
use Carbon\CarbonInterval;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class CustomerAuthController extends Controller
{
    public function __construct(
        private User $user,
        private BusinessSetting $business_setting,
        private PhoneVerification $phoneVerification,
        private LoginSetup $loginSetup,
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registration(Request $request): JsonResponse
    {
        $payload = $request->all();
        $emailInput = trim((string) ($payload['email'] ?? ''));
        if ($emailInput === '') {
            $payload['email'] = Helpers::generateUniqueAutoCustomerEmail((string) ($payload['phone'] ?? ''));
        } else {
            $payload['email'] = $emailInput;
        }

        $validator = Validator::make($payload, [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|min:11|max:14|unique:users',
            'password' => 'required|min:6',
            'user_type_id' => 'nullable|exists:user_types,id',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $defaultType = UserType::getDefault();
        $userTypeId = $request->filled('user_type_id') ? $request->user_type_id : ($defaultType?->id);

        $temporary_token = Str::random(40);
        $user = $this->user->create([
            'f_name' => $payload['f_name'],
            'l_name' => $payload['l_name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'],
            'password' => bcrypt($payload['password']),
            'temporary_token' => $temporary_token,
            'user_type_id' => $userTypeId,
            'requested_user_type_id' => null,
        ]);
        $phone_verification = (int) ($this->loginSetup->where(['key' => 'email_verification'])->first()?->value ?? 0);
        $email_verification = (int) ($this->loginSetup->where(['key' => 'phone_verification'])->first()?->value ?? 0);

        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && $user->email_verified_at == null) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    // ══════════════════════════════════════════════════════════════
    //  WhatsApp OTP — إرسال رمز تحقق عبر واتساب
    // ══════════════════════════════════════════════════════════════

    /**
     * POST /api/v1/auth/send-whatsapp-otp
     * توليد رمز OTP وإرساله عبر WaSender إلى رقم الهاتف.
     */
    public function sendWhatsAppOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:9|max:15',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $phone = trim($request->input('phone'));

        // منع إعادة الإرسال خلال 60 ثانية
        $existing = PhoneVerification::where('phone', $phone)->first();
        $resendInterval = (int) (Helpers::get_business_settings('otp_resend_time') ?? 60);
        if ($existing && Carbon::parse($existing->updated_at)->diffInSeconds(now()) < $resendInterval) {
            $wait = $resendInterval - Carbon::parse($existing->updated_at)->diffInSeconds(now());
            return response()->json([
                'errors' => [['code' => 'otp_wait', 'message' => "انتظر {$wait} ثانية قبل إعادة الإرسال"]],
            ], 429);
        }

        // توليد رمز 6 أرقام
        $otp = (string) random_int(100000, 999999);

        // حفظ / تحديث في جدول phone_verifications
        PhoneVerification::updateOrInsert(
            ['phone' => $phone],
            [
                'token'            => $otp,
                'otp_hit_count'    => 0,
                'is_temp_blocked'  => 0,
                'temp_block_time'  => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );

        // إرسال عبر WhatsApp
        $storeName = Helpers::get_business_settings('store_name') ?? 'المتجر';
        $message   = "🏪 *{$storeName}*\n"
                   . "──────────────────\n"
                   . "رمز التحقق الخاص بك:\n\n"
                   . "🔑 *{$otp}*\n\n"
                   . "صالح لمدة 10 دقائق.\n"
                   . "لا تشاركه مع أحد.\n"
                   . "──────────────────";

        $sent = (new WaSenderService())->sendMessage($phone, $message);

        if (!$sent) {
            Log::warning("WhatsApp OTP failed to send to {$phone}");
            return response()->json([
                'errors' => [['code' => 'wa_failed', 'message' => 'تعذّر إرسال رسالة واتساب. تأكد من الرقم وأعد المحاولة.']],
            ], 500);
        }

        return response()->json(['message' => 'تم إرسال رمز التحقق على واتساب', 'phone' => $phone], 200);
    }

    /**
     * POST /api/v1/auth/verify-whatsapp-otp
     * التحقق من رمز OTP المُرسَل عبر واتساب.
     */
    public function verifyWhatsAppOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp'   => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $phone = trim($request->input('phone'));
        $otp   = trim($request->input('otp'));

        $maxHits      = (int) (Helpers::get_business_settings('maximum_otp_hit') ?? 5);
        $blockSeconds = (int) (Helpers::get_business_settings('temporary_block_time') ?? 600);

        $record = PhoneVerification::where('phone', $phone)->first();

        if (!$record) {
            return response()->json([
                'errors' => [['code' => 'otp_not_found', 'message' => 'لم يُرسَل رمز لهذا الرقم، ابدأ من جديد.']],
            ], 404);
        }

        // فحص حظر مؤقت
        if ($record->temp_block_time &&
            Carbon::parse($record->temp_block_time)->diffInSeconds(now()) <= $blockSeconds) {
            $wait = $blockSeconds - Carbon::parse($record->temp_block_time)->diffInSeconds(now());
            return response()->json([
                'errors' => [['code' => 'otp_blocked', 'message' => "محظور مؤقتاً، أعد المحاولة بعد {$wait} ثانية"]],
            ], 429);
        }

        // فحص انتهاء صلاحية الرمز (10 دقائق)
        if (Carbon::parse($record->updated_at)->diffInMinutes(now()) > 10) {
            return response()->json([
                'errors' => [['code' => 'otp_expired', 'message' => 'انتهت صلاحية الرمز، اطلب رمزاً جديداً.']],
            ], 422);
        }

        if ($record->token !== $otp) {
            $hits = $record->otp_hit_count + 1;
            $update = ['otp_hit_count' => $hits];
            if ($hits >= $maxHits) {
                $update['is_temp_blocked']  = 1;
                $update['temp_block_time']  = now();
            }
            $record->update($update);

            $remaining = max(0, $maxHits - $hits);
            return response()->json([
                'errors' => [['code' => 'otp_wrong', 'message' => "رمز غير صحيح. المحاولات المتبقية: {$remaining}"]],
            ], 422);
        }

        // ✅ رمز صحيح — حذف السجل
        $record->delete();

        // إنشاء session token مؤقت لتأكيد التحقق
        $verifiedToken = hash('sha256', $phone . now()->timestamp . config('app.key'));

        return response()->json([
            'verified'        => true,
            'phone'           => $phone,
            'verified_token'  => $verifiedToken,
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function check_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:11|max:14'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phoneVerification = (int) $this->loginSetup->where(['key' => 'phone_verification'])?->first()->value ?? 0;

        if ($phoneVerification == 1) {

            $otp_interval_time= Helpers::get_business_settings('otp_resend_time') ?? 60;// seconds
            $otp_verification_data= DB::table('phone_verifications')->where('phone', $request['phone'])->first();

            if(isset($otp_verification_data) &&  Carbon::parse($otp_verification_data->created_at)->DiffInSeconds() < $otp_interval_time){
                $time= $otp_interval_time - Carbon::parse($otp_verification_data->created_at)->DiffInSeconds();

                $errors = [];
                $errors [] = [
                    'code' => 'otp',
                    'message' => translate('please_try_again_after_') . $time . ' ' . translate('seconds')
                ];

                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            return response()->json([
                'errors' => [['code' => 'sms', 'message' => translate('SMS is not configured. Phone verification is disabled.')]],
            ], 403);
        } else {
            return response()->json([
                'message' => 'Number is ready to register',
                'token' => 'inactive'
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verify_phone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $max_otp_hit = Helpers::get_business_settings('maximum_otp_hit') ?? 5;
        $max_otp_hit_time = Helpers::get_business_settings('otp_resend_time') ?? 60;// seconds
        $temp_block_time = Helpers::get_business_settings('temporary_block_time') ?? 600; // seconds

        $verify = PhoneVerification::where(['phone' => $request['phone'], 'token' => $request['token']])->first();

        if (isset($verify)) {

            if(isset($verify->temp_block_time ) && Carbon::parse($verify->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                $time = $temp_block_time - Carbon::parse($verify->temp_block_time)->DiffInSeconds();

                $errors = [];
                $errors [] = [
                    'code' => 'otp_block_time',
                    'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];

                return response()->json(['errors' => $errors], 403);
            }

            $user = $this->user->where(['phone' => $request['phone']])->first();
            $user->is_phone_verified = 1;
            $user->save();

            $verify->delete();

            $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

            return response()->json(['message' => translate('OTP verified!'), 'token' => $token, 'status' => true], 200);
        }

        else{
            $verification_data= PhoneVerification::where('phone', $request['phone'])->first();

            if(isset($verification_data)){

                if(isset($verification_data->temp_block_time ) && Carbon::parse($verification_data->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors [] = [
                        'code' => 'otp_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                if($verification_data->is_temp_blocked == 1 && Carbon::parse($verification_data->updated_at)->DiffInSeconds() >= $temp_block_time){
                    DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                        [
                            'otp_hit_count' => 0,
                            'is_temp_blocked' => 0,
                            'temp_block_time' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                if($verification_data->otp_hit_count >= $max_otp_hit &&  Carbon::parse($verification_data->updated_at)->DiffInSeconds() < $max_otp_hit_time &&  $verification_data->is_temp_blocked == 0){

                    DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                        [
                            'is_temp_blocked' => 1,
                            'temp_block_time' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                    $time = $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors [] = [
                        'code' => 'otp_temp_blocked',
                        'message' => translate('Too_many_attempts. Please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }
            }

            DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                [
                    'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                    'updated_at' => now(),
                    'temp_block_time' => null,
                ]);
        }

        return response()->json(['errors' => [
            ['code' => 'token', 'message' => 'OTP is not matched!']
        ]], 403);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function check_email(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $emailVerification = (int) $this->loginSetup->where(['key' => 'email_verification'])?->first()->value ?? 0;

        if ($emailVerification) {

            $otp_interval_time= Helpers::get_business_settings('otp_resend_time') ?? 60;// seconds
            $otp_verification_data= DB::table('email_verifications')->where('email', $request['email'])->first();

            if(isset($otp_verification_data) &&  Carbon::parse($otp_verification_data->created_at)->DiffInSeconds() < $otp_interval_time){
                $time= $otp_interval_time - Carbon::parse($otp_verification_data->created_at)->DiffInSeconds();

                $errors = [];
                $errors [] = [
                    'code' => 'otp',
                    'message' => translate('please_try_again_after_') . $time . ' ' . translate('seconds')
                ];

                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            $token = Helpers::generateOtpToken();

            DB::table('email_verifications')->updateOrInsert(['email' => $request['email']], [
                'email' => $request['email'],
                'token' => $token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (config('app.mode') == 'local') {
                Log::info('Email verification OTP sent (local mode – not sent by email)', ['email' => $request['email']]);
            } else {
                try {
                    $emailServices = Helpers::get_business_settings('mail_config');
                    if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                        Mail::to($request['email'])->send(new EmailVerification($token));
                    }
                } catch (\Exception $exception) {
                    return response()->json([
                        'message' => 'Token sent failed'
                    ], 403);
                }
            }

            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'active'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Email is ready to register',
                'token' => 'inactive'
            ], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verify_email(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $max_otp_hit = Helpers::get_business_settings('maximum_otp_hit') ?? 5;
        $max_otp_hit_time = Helpers::get_business_settings('otp_resend_time') ?? 60;// seconds
        $temp_block_time = Helpers::get_business_settings('temporary_block_time') ?? 600; // seconds

        $verify = EmailVerifications::where(['email' => $request['email'], 'token' => $request['token']])->first();

        if (isset($verify)) {

            if(isset($verify->temp_block_time ) && Carbon::parse($verify->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                $time = $temp_block_time - Carbon::parse($verify->temp_block_time)->DiffInSeconds();

                $errors = [];
                $errors [] = [
                    'code' => 'otp_block_time',
                    'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];

                return response()->json([
                    'errors' => $errors
                ], 403);
            }

            $user = $this->user->where(['email' => $request['email']])->first();
            $user->email_verified_at = Carbon::now();
            $user->save();

            $verify->delete();

            $token = $user->createToken('RestaurantCustomerAuth')->accessToken;

            return response()->json(['message' => translate('OTP verified!'), 'token' => $token, 'status' => true], 200);
        }
        else{
            $verification_data= DB::table('email_verifications')->where('email', $request['email'])->first();

            if(isset($verification_data)){
                if(isset($verification_data->temp_block_time ) && Carbon::parse($verification_data->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                    $time= $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors [] = [
                        'code' => 'otp_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                if($verification_data->is_temp_blocked == 1 && Carbon::parse($verification_data->updated_at)->DiffInSeconds() >= $temp_block_time){
                    DB::table('email_verifications')->updateOrInsert(['email' => $request['email']],
                        [
                            'otp_hit_count' => 0,
                            'is_temp_blocked' => 0,
                            'temp_block_time' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                if($verification_data->otp_hit_count >= $max_otp_hit &&  Carbon::parse($verification_data->updated_at)->DiffInSeconds() < $max_otp_hit_time &&  $verification_data->is_temp_blocked == 0){

                    DB::table('email_verifications')->updateOrInsert(['email' => $request['email']],
                        [
                            'is_temp_blocked' => 1,
                            'temp_block_time' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                    $time = $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors [] = [
                        'code' => 'otp_temp_blocked',
                        'message' => translate('Too_many_attempts. Please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }
            }

            DB::table('email_verifications')->updateOrInsert(['email' => $request['email']],
                [
                    'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                    'updated_at' => now(),
                    'temp_block_time' => null,
                ]);
        }

        return response()->json(['errors' => [
            ['code' => 'otp', 'message' => 'OTP is not matched!']
        ]], 403);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
            'password' => 'required|min:6',
            'type' => 'required|in:phone,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = $request['email_or_phone'];
        $type = $request['type'];

        $user = $this->user
            ->where(function ($query) use ($userId) {
                $query->where(['email' => $userId])->orWhere('phone', $userId);
            })->first();

        $max_login_hit = Helpers::get_business_settings('maximum_login_hit') ?? 5;
        $temp_block_time = Helpers::get_business_settings('temporary_login_block_time') ?? 600; // seconds

        if (isset($user)) {
            if(isset($user->temp_block_time ) && Carbon::parse($user->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                $time = $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();

                $errors = [];
                $errors [] = [
                    'code' => 'login_block_time',
                    'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];

                return response()->json(['errors' => $errors], 403);
            }

            $user->temporary_token = Str::random(40);
            $user->save();

            if ($type == 'phone'){
                $data = [
                    'phone' => $user->phone,
                    'password' => $request->password,
                ];
            }elseif ($type == 'email'){
                $data = [
                    'email' => $user->email,
                    'password' => $request->password,
                ];
            }

            if (auth()->attempt($data)) {
                $temporary_token = Str::random(40);

                $phone_verification = (int) $this->loginSetup->where(['key' => 'phone_verification'])?->first()->value ?? 0;
                $email_verification = (int) $this->loginSetup->where(['key' => 'email_verification'])?->first()->value ?? 0;

                if ($type == 'phone' && $phone_verification && !$user->is_phone_verified) {
                    return response()->json(['temporary_token' => $temporary_token, 'status' => false], 200);
                }
                if ($type == 'email' && $email_verification && $user->email_verified_at == null) {
                    return response()->json(['temporary_token' => $temporary_token, 'status' => false], 200);
                }

                $token = auth()->user()->createToken('RestaurantCustomerAuth')->accessToken;

                $user->login_hit_count = 0;
                $user->is_temp_blocked = 0;
                $user->temp_block_time = null;
                $user->updated_at = now();
                $user->save();

                return response()->json(['token' => $token, 'status' => true], 200);
            }

            else{
                $customer = $this->user->where(['email' => $userId])->orWhere(['phone' => $userId])->first();

                if(isset($customer)){

                    if(isset($user->temp_block_time ) && Carbon::parse($user->temp_block_time)->DiffInSeconds() <= $temp_block_time){
                        $time= $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();

                        $errors = [];
                        $errors [] = [
                            'code' => 'login_block_time',
                            'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                        ];

                        return response()->json([
                            'errors' => $errors
                        ], 403);
                    }

                    if($user->is_temp_blocked == 1 && Carbon::parse($user->temp_block_time)->DiffInSeconds() >= $temp_block_time){

                        $user->login_hit_count = 0;
                        $user->is_temp_blocked = 0;
                        $user->temp_block_time = null;
                        $user->updated_at = now();
                        $user->save();
                    }

                    if($user->login_hit_count >= $max_login_hit &&  $user->is_temp_blocked == 0){
                        $user->is_temp_blocked = 1;
                        $user->temp_block_time = now();
                        $user->updated_at = now();
                        $user->save();

                        $time= $temp_block_time - Carbon::parse($user->temp_block_time)->DiffInSeconds();

                        $errors = [];
                        $errors [] = [
                            'code' => 'login_temp_blocked',
                            'message' => translate('Too_many_attempts. Please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()
                        ];
                        return response()->json([
                            'errors' => $errors
                        ], 403);
                    }
                }

                $user->login_hit_count += 1;
                $user->temp_block_time = null;
                $user->updated_at = now();
                $user->save();
            }
        }

        $errors = [];
        $errors [] = ['code' => 'auth-001', 'message' => translate('Invalid credential.')];
        return response()->json([
            'errors' => $errors
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function social_customer_login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'unique_id' => 'required',
            'email' => 'required_if:medium,google,facebook',
            'medium' => 'required|in:google,facebook,apple',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $client = new Client();
        $token = $request['token'];
        $email = $request['email'];
        $uniqueId = $request['unique_id'];

        try {
            if ($request['medium'] == 'google') {
                $res = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . $token);
                $data = json_decode($res->getBody()->getContents(), true);
            } elseif ($request['medium'] == 'facebook') {
                $res = $client->request('GET', 'https://graph.facebook.com/' . $uniqueId . '?access_token=' . $token . '&&fields=name,email');
                $data = json_decode($res->getBody()->getContents(), true);
            }elseif ($request['medium'] == 'apple') {
                $appleLogin = Helpers::get_business_settings('apple_login');
                $teamId = $appleLogin['team_id'];
                $keyId = $appleLogin['key_id'];
                $sub = $appleLogin['client_id'];
                $aud = 'https://appleid.apple.com';
                $iat = strtotime('now');
                $exp = strtotime('+60days');
                $keyContent = file_get_contents('storage/app/public/apple-login/'.$appleLogin['service_file']);

                $token = JWT::encode([
                    'iss' => $teamId,
                    'iat' => $iat,
                    'exp' => $exp,
                    'aud' => $aud,
                    'sub' => $sub,
                ], $keyContent, 'ES256', $keyId);

                $redirectUri = $appleLogin['redirect_url']??'www.example.com/apple-callback';

                $res = Http::asForm()->post('https://appleid.apple.com/auth/token', [
                    'grant_type' => 'authorization_code',
                    'code' => $uniqueId,
                    'redirect_uri' => $redirectUri,
                    'client_id' => $sub,
                    'client_secret' => $token,
                ]);

                $claims = explode('.', $res['id_token'])[1];
                $data = json_decode(base64_decode($claims),true);
            }
        } catch (\Exception $exception) {
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Invalid Token'];
            return response()->json([
                'errors' => $errors
            ], 401);
        }

        if (!isset($claims)) {
            if (strcmp($email, $data['email']) != 0) {
                return response()->json(['error' => translate('email_does_not_match')], 403);
            }
        }

        $existingUser =  $this->user->where('email', $data['email'])->first();
        $temporaryToken = Str::random(40);

        if (!$existingUser){
            if ($request['medium'] == 'apple'){
                return response()->json(['temp_token' => $temporaryToken, 'email' => $data['email'], 'status' => false], 200);
            }
            return response()->json(['temp_token' => $temporaryToken, 'status' => false], 200);
        }

        if ($existingUser->email_verified_at != null){
            $token = $existingUser->createToken('RestaurantCustomerAuth')->accessToken;
            if ($request['medium'] == 'apple'){
                return response()->json(['token' => $token, 'email' => $data['email'], 'status' => true], 200);
            }
            return response()->json(['token' => $token, 'status' => true], 200);
        }else{
            if ($request['medium'] == 'apple'){
                return response()->json(['user' => $existingUser, 'email' => $data['email'], 'status' => false], 200);
            }
            return response()->json(['user' => $existingUser, 'status' => false], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function firebaseAuthVerify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sessionInfo' => 'required',
            'phoneNumber' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $firebaseOTPVerification = Helpers::get_business_settings('firebase_otp_verification');
        $webApiKey = $firebaseOTPVerification ? $firebaseOTPVerification['web_api_key'] : '';

        $response = Http::post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key='. $webApiKey, [
            'sessionInfo' => $request->sessionInfo,
            'phoneNumber' => $request->phoneNumber,
            'code' => $request->code,
        ]);

        $responseData = $response->json();

        if (isset($responseData['error'])) {
            $errors = [];
            $errors[] = ['code' => "403", 'message' => $responseData['error']['message']];
            return response()->json(['errors' => $errors], 403);
        }

        $user = $this->user->where('phone', $responseData['phoneNumber'])->first();

        if (isset($user)){
            if ($request['is_reset_token'] == 1){
                DB::table('password_resets')->updateOrInsert(['email_or_phone' => $request->phoneNumber], [
                    'email_or_phone' => $request->phoneNumber,
                    'token' => $request->code,
                    'created_at' => now(),
                ]);
            }else{
                $token = $user->createToken('AuthToken')->accessToken;
                $user->is_phone_verified = 1;
                $user->save();
                return response()->json(['errors' => null, 'token' => $token], 200);
            }
        }

        $tempToken = Str::random(120);
        return response()->json(['errors' => null, 'temp_token' => $tempToken], 200);
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $maxOTPHit = Helpers::get_business_settings('maximum_otp_hit') ?? 5;
        $maxOTPHitTime = Helpers::get_business_settings('otp_resend_time') ?? 60;// seconds
        $tempBlockTime = Helpers::get_business_settings('temporary_block_time') ?? 600; // seconds

        $verify = $this->phoneVerification->where(['phone' => $request['phone'], 'token' => $request['token']])->first();

        if (isset($verify)) {
            if(isset($verify->temp_block_time ) && Carbon::parse($verify->temp_block_time)->DiffInSeconds() <= $tempBlockTime){
                $time = $tempBlockTime - Carbon::parse($verify->temp_block_time)->DiffInSeconds();

                $errors = [];
                $errors[] = ['code' => 'otp_block_time',
                    'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                ];
                return response()->json(['errors' => $errors], 403);
            }

            $verify->delete();

            $temporaryToken = Str::random(40);

            $isUserExist = $this->user->where(['phone' => $request['phone']])->first();
            if (!$isUserExist){
                return response()->json(['temporary_token' => $temporaryToken, 'status' => false], 200);
            }

            $isUserExist->is_phone_verified = 1;
            $isUserExist->save();

            $token = $isUserExist->createToken('RestaurantCustomerAuth')->accessToken;

            return response()->json(['token' => $token, 'status' => true], 200);

        }else{
            $verificationdata = DB::table('phone_verifications')->where('phone', $request['phone'])->first();

            if(isset($verificationdata)){
                if(isset($verificationdata->temp_block_time ) && Carbon::parse($verificationdata->temp_block_time)->DiffInSeconds() <= $tempBlockTime){
                    $time= $tempBlockTime - Carbon::parse($verificationdata->temp_block_time)->DiffInSeconds();

                    $errors = [];
                    $errors[] = ['code' => 'otp_block_time',
                        'message' => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans()
                    ];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

                if($verificationdata->is_temp_blocked == 1 && Carbon::parse($verificationdata->updated_at)->DiffInSeconds() >= $tempBlockTime){
                    DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                        [
                            'otp_hit_count' => 0,
                            'is_temp_blocked' => 0,
                            'temp_block_time' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                if($verificationdata->otp_hit_count >= $maxOTPHit &&  Carbon::parse($verificationdata->updated_at)->DiffInSeconds() < $maxOTPHitTime &&  $verificationdata->is_temp_blocked == 0){

                    DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                        [
                            'is_temp_blocked' => 1,
                            'temp_block_time' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                    $time = $tempBlockTime - Carbon::parse($verificationdata->temp_block_time)->DiffInSeconds();
                    $errors = [];
                    $errors[] = ['code' => 'otp_temp_blocked', 'message' => translate('Too_many_attempts. please_try_again_after_'). CarbonInterval::seconds($time)->cascade()->forHumans()];
                    return response()->json([
                        'errors' => $errors
                    ], 403);
                }

            }

            DB::table('phone_verifications')->updateOrInsert(['phone' => $request['phone']],
                [
                    'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                    'updated_at' => now(),
                    'temp_block_time' => null,
                ]);
        }

        return response()->json(['errors' => [
            ['code' => 'token', 'message' => translate('OTP is not matched!')]
        ]], 403);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registrationWithOTP(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|max:255',
            'phone' => 'required|string|max:15',
            'user_type_id' => 'nullable|exists:user_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $resolvedEmail = trim((string) $request->input('email', ''));
        if ($resolvedEmail === '') {
            $resolvedEmail = Helpers::generateUniqueAutoCustomerEmail((string) $request->phone);
        } else {
            $isEmailExist = $this->user->where(['email' => $resolvedEmail])->first();

            if ($isEmailExist){
                return response()->json(['errors' => [
                    ['code' => 'email', 'message' => translate('This email has already been used in another account!')]
                ]], 403);
            }
        }

        $temporaryToken = Str::random(40);

        $name = $request->name;
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $defaultType = UserType::getDefault();
        $userTypeId = $request->filled('user_type_id') ? $request->user_type_id : ($defaultType?->id);

        $user = new User();
        $user->f_name = $firstName;
        $user->l_name = $lastName;
        $user->email = $resolvedEmail;
        $user->phone = $request->phone;
        $user->password = bcrypt(rand(11111111, 99999999));
        $user->temporary_token = $temporaryToken;
        $user->is_phone_verified = 1;
        $user->login_medium = 'OTP';
        $user->user_type_id = $userTypeId;
        $user->requested_user_type_id = null;
        $user->save();

        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function existingAccountCheck(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_response' => 'required|in:0,1',
            'medium' => 'required|in:google,facebook,apple',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = $this->user->where('email', $request['email'])->first();

        $temporaryToken = Str::random(40);
        if (!$user) {
            return response()->json(['temp_token' => $temporaryToken, 'status' => false], 200);
        }

        if ($request['user_response'] == 1) {
            $user->email_verified_at = now();
            $user->login_medium = $request['medium'];
            $user->save();

            $token = $user->createToken('RestaurantCustomerAuth')->accessToken;
            return response()->json(['token' => $token, 'status' => true], 200);
        }

        return response()->json(['temp_token' => $temporaryToken, 'status' => false], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function registrationWithSocialMedia(Request $request): JsonResponse
    {
        $payload = $request->all();
        $emailInput = trim((string) ($payload['email'] ?? ''));
        if ($emailInput === '') {
            $payload['email'] = Helpers::generateUniqueAutoCustomerEmail((string) ($payload['phone'] ?? ''));
        } else {
            $payload['email'] = $emailInput;
        }

        $validator = Validator::make($payload, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|max:15',
            'user_type_id' => 'nullable|exists:user_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $isPhoneExist = $this->user->where(['phone' => $payload['phone']])->first();

        if ($isPhoneExist){
            return response()->json(['errors' => [
                ['code' => 'email', 'message' => translate('This phone has already been used in another account!')]
            ]], 403);
        }

        $temporaryToken = Str::random(40);

        $existingUser = $this->user->where('email', $payload['email'])->first();
        if ($existingUser){
            $existingUser->email = null;
            $existingUser->email_verified_at = null;
            $existingUser->save();
        }

        $name = $payload['name'];
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $defaultType = UserType::getDefault();
        $userTypeId = $request->filled('user_type_id') ? $request->user_type_id : ($defaultType?->id);

        $user = new User();
        $user->f_name = $firstName;
        $user->l_name = $lastName;
        $user->email = $payload['email'];
        $user->phone = $payload['phone'];
        $user->password = bcrypt(rand(11111111, 99999999));
        $user->temporary_token = $temporaryToken;
        $user->email_verified_at = now();
        $user->login_medium = 'social';
        $user->user_type_id = $userTypeId;
        $user->requested_user_type_id = null;
        $user->save();

        $phoneVerificationStatus = (int) $this->loginSetup->where(['key' => 'phone_verification'])?->first()->value ?? 0;
        if ($phoneVerificationStatus){
            return response()->json(['temp_token' => $temporaryToken, 'status' => false], 200);
        }

        $token = $user->createToken('RestaurantCustomerAuth')->accessToken;
        return response()->json(['token' => $token], 200);
    }

}
