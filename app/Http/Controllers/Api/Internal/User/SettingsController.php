<?php

namespace App\Http\Controllers\Api\Internal\User;

use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Requests\UserSettingsRequest;
use App\Models\User\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends ApiController
{
    /**
     * @OA\Get(path="/api/user/settings",
     *     tags={"User Info"},
     *     summary="Get User's settings",
     *     description="Get User's settings",
     *     @OA\Response(response="200", description="User's settings list successfully extracted",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="settings", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function getSettings(): JsonResponse
    {
        return $this->success(['settings' => Setting::byUser(auth()->id())]);
    }

    /**
     * @OA\Put(path="/api/user/settings",
     *     tags={"User Info"},
     *     summary="Set User's settings",
     *     description="Set User's settings",
     *     @OA\Response(response="200", description="Settings were successfully saved",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="settings", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function storeSettings(UserSettingsRequest $request)
    {
        collect($request)->each(function ($value, $key) {
            tap(Setting::firstOrNew(['user_id' => auth()->id(), 'key' => $key]), function (Setting $setting) use ($value) {
                $setting->value = $value;
                $setting->description = '';
                $setting->save();
            });
        });

        return $this->success([
            'message' => 'Settings were saved',
            'settings' => Setting::byUser(auth()->id()),
        ]);
    }
}
