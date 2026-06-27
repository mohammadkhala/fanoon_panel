<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Admin;
use App\Models\BusinessSetting;
use App\Models\Conversation;
use App\Models\Message;
use App\Traits\UploadSizeHelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Admin           $admin,
        private BusinessSetting $businessSetting,
        private Conversation    $conversation,
        private Message         $message
    )
    {
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getAdminMessage(Request $request): array
    {
        $limit = Helpers::capApiLimit($request->limit ?? 10);
        $offset = Helpers::capApiOffset($request->offset ?? 1);
        $messages = $this->conversation->where(['user_id' => $request->user()->id])->latest()->paginate($limit, ['*'], 'page', $offset);
        $messages = ConversationResource::collection($messages);
        return [
            'total_size' => $messages->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'messages' => $messages->items()
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeAdminMessage(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $this->validateUploadedFile($request, ['image']);

        $validator = Validator::make($request->all(), [
            'message'  => 'required_without_all:image|nullable|string',
            'image'   => 'sometimes|array',
            'image.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $imageNameId = [];
            if (!empty($request->file('image'))) {
                foreach ($request->image as $img) {
                    $image = Helpers::upload('conversation/', APPLICATION_IMAGE_FORMAT, $img);
                    $imageUrl = asset('storage/conversation') . '/' . $image;
                    $imageNameId[] = $imageUrl;
                }
                $images = $imageNameId;
            } else {
                $images = null;
            }
            $conv = $this->conversation;
            $conv->user_id = $request->user()->id;
            $conv->message = $request->message;
            $conv->image = null;
            $conv->attachment = isset($images) ? json_encode($images) : null;
            $conv->save();

            $admin = $this->admin->first();
            $data = [
                'title' => $request->user()->f_name . ' ' . $request->user()->l_name . \App\CentralLogics\translate(' send a message'),
                'description' => $request->user()->id,
                'order_id' => '',
                'image' => asset('storage/ecommerce') . '/' . ($this->businessSetting->where(['key' => 'logo'])->first()?->value ?? ''),
                'type' => 'message',
            ];
            try {
                Helpers::send_push_notif_to_device($admin->fcm_token, $data);
            } catch (\Exception $exception) {
            }

            return response()->json(['message' => translate('Successfully sent')], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

    }

}
