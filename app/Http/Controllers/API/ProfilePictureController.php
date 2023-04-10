<?php

namespace App\Http\Controllers\API;

use App\Models\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilePictureController extends Controller
{
    //

    //create annotation for upload profile picture

    /**
     * @OA\Post(
     *    path="/api/profile-photo/upload-picture",
     *   summary="Upload profile picture",
     *  tags={"Profile Picture"},
     * security={{"Bearer":{}}},
     * description="Upload profile picture",
     * operationId="uploadProfilePicture",
     * 
     * @OA\RequestBody(
     *   required=true,
     * 
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(
     * property="image",
     * type="file",
     * format="file",
     * description="Profile picture"
     * )
     * )
     * )
     * ),
     * 
     * @OA\Response(
     *  response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Profile picture uploaded successfully."
     * )
     * )
     * 
     * )
     * )
     * )    
     */

    public function upload(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $imagePath = $request->file('image')->store('profile_pictures', 'public');

        $profilePicture = Photo::updateOrCreate(
            ['user_id' => $user->id],
            ['image_path' => $imagePath]
        );

        return response()->json(['message' => 'Profile picture uploaded successfully.']);
    }

    public function getProfilePicture(Request $request)
    {
        $user = $request->user();

        $profilePicture = Photo::where('user_id', $user->id)->first();

        if ($profilePicture) {
            return response()->json(['profile_picture' => $profilePicture->image_path]);
        }

        return response()->json(['message' => 'No profile picture found.']);
    }

    //create annotation for show profile picture

    /**
     * @OA\Get(
     *    path="/api/profile-photo/show-profile-picture",
     *   summary="Show profile picture",
     *  tags={"Profile Picture"},
     * security={{"Bearer":{}}},
     * description="Show profile picture",
     * operationId="showProfilePicture",
     * 
     * @OA\Response(
     *  response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Profile picture uploaded successfully."
     * )
     * )
     * 
     * )
     * )
     * )    
     */
    public function show(Request $request)
    {

        $user_id = Auth::user()->id;
        $profilePicture = Photo::where('user_id', $user_id)->first();

        if (!$profilePicture) {
            return response()->json(['message' => 'No profile picture found.']);
        }

        $imagePath = Storage::url($profilePicture->image_path);

        return response()->file(public_path($imagePath));
    }
}
