<?php

namespace App\Http\Controllers\Security;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use App\Models\Security\TwoStepVerification;
use OpenApi\Annotations as OA;

class SecurityController extends BaseController
{
    //

    //create annotation for change password
    /**
     * @OA\Post(
     *    path="/api/security/change-password",
     *   summary="Change password",
     * tags={"Security"},
     * description="Change password",
     * operationId="changePassword",
     * @OA\RequestBody(
     *   required=true,
     * @OA\JsonContent(
     * required={"current_password", "new_password", "confirm_password"},
     * @OA\Property(
     * property="current_password",
     * type="string",
     * example="12345678"
     * ),
     * @OA\Property(
     * property="new_password",
     * type="string",
     * example="12345678"
     * ),
     * @OA\Property(
     * property="confirm_password",
     * type="string",
     * example="12345678"
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     *  
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean",
     * example=true
     * ),
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Unauthenticated."
     * )
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Forbidden."
     * )
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Validation Error."
     * ),
     * @OA\Property(
     * property="errors",
     * type="object",
     * @OA\Property(
     * property="current_password",
     * type="array",
     * @OA\Items(
     * type="string",
     * example="The current password field is required."
     * )
     * ),
     * @OA\Property(
     * property="new_password",
     * type="array",
     * @OA\Items(
     * type="string",
     * example="The new password field is required."
     * )
     * ),
     * @OA\Property(
     * property="confirm_password",
     * type="array",
     * @OA\Items(
     * type="string",
     * example="The confirm password field is required."
     * )
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Internal Server Error."
     * )
     * )
     * )
     * )
     * 
     * 
     */
    public function changePassword(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8'],
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = auth()->user();

        $user = User::find($user->id);
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);

            $user->save();

            return $this->sendResponse($user, 'Password changed successfully.', 200);
        } else {
            return $this->sendError('Validation Error.', ['current_password' => 'Current password is incorrect'], 422);
        }
    }

    //create annotation for two step verification

    /**
     * 
     * @OA\Post(
     *   path="/api/security/two-step-verification",
     *  summary="Two step verification",
     * tags={"Security"},
     * security={{"Bearer":{}}},
     * description="Two step verification",
     * operationId="twoStepVerification",
     * @OA\RequestBody(
     *  required=true,
     * @OA\JsonContent(
     * required={"enabled"},
     * @OA\Property(
     * property="enabled",
     * type="boolean",
     * example=true
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     * 
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean",
     * example=true
     * ),
     * @OA\Property(
     * 
     * 
     * property="data",
     * type="object",
     * @OA\Property(
     * property="id",
     * type="integer",
     * example=1
     * ),
     * @OA\Property(
     * property="user_id",
     * type="integer",
     * example=1
     * ),
     * @OA\Property(
     * property="enabled",
     * type="boolean",
     * example=true
     * ),
     * 
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Unauthenticated."
     * )
     * 
     * )
     * 
     * ),
     * @OA\Response(
     * 
     *  response=403,
     * description="Forbidden",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Forbidden."
     * )
     * 
     * )
     * 
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Validation Error."
     * ),
     * @OA\Property(
     * property="errors",
     * type="object",
     * @OA\Property(
     * property="enabled",
     * type="array",
     * @OA\Items(
     * type="string",
     * example="The enabled field is required."
     * )
     * )
     * )
     * )
     * )
     * )
     * 
     * 
     * 
     * 
     */
    public function setTwoStepVerification(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'enabled' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = auth()->user();
        $user = User::find($user->id);
        $twoFactorVerification = TwoStepVerification::where('user_id', $user->id)->first();
        $twoFactorVerification->enabled = $request->input('enabled');
        $twoFactorVerification->save();

        return $this->sendResponse($twoFactorVerification, 'Two step verification updated successfully.', 200);
    }

    //create annotation for get two step verification

    /**
     * 
     * @OA\Get(
     *   path="/api/security/two-step-verification",
     *  summary="Get two step verification",
     * tags={"Security"},
     * security={{"Bearer":{}}},
     * description="Get two step verification",
     * operationId="getTwoStepVerification",
     * @OA\Response(
     * response=200,
     * description="Success",
     * 
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean",
     * example=true
     * ),
     * @OA\Property(
     * 
     * 
     * property="data",
     * type="object",
     * @OA\Property(
     * property="id",
     * type="integer",
     * example=1
     * ),
     * @OA\Property(
     * property="user_id",
     * type="integer",
     * example=1
     * ),
     * @OA\Property(
     * property="enabled",
     * type="boolean",
     * example=true
     * ),
     * 
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Unauthenticated."
     * )
     * 
     * )
     * 
     * ),
     * @OA\Response(
     * 
     *  response=403,
     * description="Forbidden",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Forbidden."
     * )
     * 
     * )
     * 
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Validation Error."
     * ),
     * @OA\Property(
     * property="errors",
     * type="object",
     * @OA\Property(
     * property="enabled",
     * type="array",
     * @OA\Items(
     * type="string",
     * example="The enabled field is required."
     * )
     * )
     * )
     * )
     * )
     * )
     */ 
    public function getCurrentTwoStepVerification()
    {
        $user = auth()->user();
        $user = User::find($user->id);
        $twoFactorVerification = TwoStepVerification::where('user_id', $user->id)->first();

        return $this->sendResponse($twoFactorVerification, 'Two step verification fetched successfully.', 200);
    }
}
