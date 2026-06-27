<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\CustomerAddress;
use App\Models\EmailVerifications;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointLog;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Traits\UploadSizeHelperTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Factory;
use function App\Library\responseFormatter;

class CustomerController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private CustomerAddress   $customerAddress,
        private User              $user
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addressList(Request $request): JsonResponse
    {
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $addresses = $this->customerAddress
            ->where(['user_id' => $userId, 'is_guest' => $userType])
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($addresses, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addNewAddress(Request $request): JsonResponse
    {
        $rules = [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'city' => 'required',
            'address' => 'required',
        ];
        if (Schema::hasTable('areas')) {
            $rules['area_id'] = 'nullable|exists:areas,id';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $address = [
            'user_id' => $userId,
            'is_guest' => $userType,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'city' => $request->city,
            'address_type' => $request->address_type ?? 'Other',
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        if (Schema::hasColumn('customer_addresses', 'area_id')) {
            $address['area_id'] = $request->area_id ?? null;
        }
        DB::table('customer_addresses')->insert($address);
        return response()->json(['message' => 'successfully added!'], 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function updateAddress(Request $request, $id): JsonResponse
    {
        $rules = [
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'city' => 'required',
            'address' => 'required',
        ];
        if (Schema::hasTable('areas')) {
            $rules['area_id'] = 'nullable|exists:areas,id';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $address = [
            'user_id' => $userId,
            'is_guest' => $userType,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'city' => $request->city,
            'address_type' => $request->address_type ?? 'Other',
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'updated_at' => now()
        ];
        if (Schema::hasColumn('customer_addresses', 'area_id')) {
            $address['area_id'] = $request->area_id ?? null;
        }
        DB::table('customer_addresses')->where('id', $id)->update($address);
        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if (DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $userId, 'is_guest' => $userType])->first()) {
            DB::table('customer_addresses')->where(['id' => $request['address_id'], 'user_id' => $userId, 'is_guest' => $userType])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => 'No such data found!'], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $customer = User::with(['userType', 'requestedUserType', 'loyaltyPoint'])->find(auth('api')->id());
        return response()->json(CustomerResource::make($customer), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $this->validateUploadedFile($request, ['image']);

        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'phone' => 'required|max:14|unique:users,phone,' . $request->user()->id,
            'password' => 'nullable|string|min:6',
            'image' =>'nullable|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),

        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = Helpers::update('profile/', $request->user()->image, APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        $user = $this->user->find($request->user()->id);
        if (!$user) {
            return response()->json(['message' => translate('User not found')], 200);
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $password = bcrypt($request['password']);
        } else {
            $password = $request->user()->password;
        }

        $user->f_name = $request->f_name;
        $user->l_name = $request->l_name;

        if ($user->email != $request['email']) {
            $user->email_verified_at = null;
            $user->email = $request->email;
        }

        $user->phone = $request->phone;
        $user->image = $imageName;
        $user->password = $password;
        $user->update();

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCmFirebaseToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        DB::table('users')->where('id', $request->user()->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token']
        ]);

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeAccount(Request $request): JsonResponse
    {
        $customer = $this->user->find($request->user()->id);
        if (isset($customer)) {
            Helpers::file_remover('customer/', $customer->image);
            $customer->delete();

        } else {
            return response()->json(['status_code' => 404, 'message' => translate('Not found')], 200);
        }

        return response()->json(['status_code' => 200, 'message' => translate('Successfully deleted')], 200);
    }

    public function fcmSubscribeToTopic(Request $request): JsonResponse|bool|string
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'topic' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $serviceAccountContent = Helpers::get_business_settings('push_notification_service_file_content');
        $serviceAccount = is_array($serviceAccountContent) ? $serviceAccountContent : json_decode($serviceAccountContent, true);
        $factory = (new Factory)->withServiceAccount($serviceAccount);
        $messaging = $factory->createMessaging();

        $token = $request->input('token');
        $topic = $request->input('topic');

        try {
            $messaging->subscribeToTopic($topic, $token);
            return response()->json(['message' => 'Successfully subscribed to topic ' . $topic], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyProfileInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:phone,email,firebase',
            'sessionInfo' => 'required_if:type,firebase',
            'email_or_phone' => 'required',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $type = $request['type'];

        if ($type == 'firebase') {
            $firebaseOTPVerification = Helpers::get_business_settings('firebase_otp_verification');
            $webApiKey = $firebaseOTPVerification ? $firebaseOTPVerification['web_api_key'] : '';

            $response = Http::post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key=' . $webApiKey, [
                'sessionInfo' => $request->sessionInfo,
                'phoneNumber' => $request['email_or_phone'],
                'code' => $request->token,
            ]);

            $responseData = $response->json();

            if (isset($responseData['error'])) {
                $errors = [];
                $errors[] = ['code' => "403", 'message' => $responseData['error']['message']];
                return response()->json(['errors' => $errors], 403);
            }

            $user = $this->user->find($request->user()->id);
            $user->phone = $request['email_or_phone'];
            $user->is_phone_verified = 1;
            $user->save();

            return response()->json(['message' => translate('Phone number is successfully verified')], 200);
        }

        if ($type == 'phone') {
            $verificationData = PhoneVerification::where(['phone' => $request['email_or_phone'], 'token' => $request['token']])->first();

            if (!$verificationData) {
                return response()->json(['errors' => [
                    ['code' => 'token', 'message' => translate('OTP is not matched!')]
                ]], 403);
            }

            $user = $this->user->find($request->user()->id);
            $user->phone = $request['email_or_phone'];
            $user->is_phone_verified = 1;
            $user->save();

            $verificationData->delete();
            return response()->json(['message' => translate('Phone number is successfully verified')], 200);
        }

        if ($type == 'email') {
            $verificationData = EmailVerifications::where(['email' => $request['email_or_phone'], 'token' => $request['token']])->first();

            if (!$verificationData) {
                return response()->json(['errors' => [
                    ['code' => 'token', 'message' => translate('OTP is not matched!')]
                ]], 403);
            }

            $user = $this->user->find($request->user()->id);
            $user->email = $request['email_or_phone'];
            $user->email_verified_at = now();
            $user->save();

            $verificationData->delete();
            return response()->json(['message' => translate('Email is successfully verified')], 200);
        }
    }

    /**
     * Get customer loyalty points balance and level.
     */
    public function loyalty(Request $request): JsonResponse
    {
        $lp = LoyaltyPoint::where('user_id', auth('api')->id())->first();

        $redemptionValue = (float) (Helpers::get_business_settings('loyalty_point_redemption_value') ?? 0.5);
        $enabled = (int) (Helpers::get_business_settings('loyalty_points_enabled') ?? 0);

        return response()->json([
            'loyalty_points_enabled' => $enabled === 1,
            'points' => $lp?->points ?? 0,
            'level' => $lp?->level ?? 'bronze',
            'total_spent' => (float) ($lp?->total_spent ?? 0),
            'redemption_value_per_point' => $redemptionValue,
            'levels' => LoyaltyPoint::LEVELS,
        ], 200);
    }

    /**
     * Get customer loyalty points history.
     */
    public function loyaltyHistory(Request $request): JsonResponse
    {
        $perPage = \App\CentralLogics\Helpers::capApiLimit($request->get('per_page', 15));
        $logs = LoyaltyPointLog::where('user_id', auth('api')->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($logs, 200);
    }
}
