<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\AdminInformation;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use OpenApi\Annotations as OA;

class AdminInformationController extends BaseController
{
    //create swagger annotation for store method

    /**
     * @OA\Post(
     *     path="/api/admin/store-admin-information",
     *    tags={"Admin Information"},
     *     summary="Create Admin Information",
     *    description="Create Admin Information",
     *    operationId="createAdminInformation",
     *    security={{"Bearer":{}}},
     *    @OA\RequestBody(
     *       required=true,
     *      @OA\JsonContent(
     *         required={"first_name", "last_name", "email", "phone_number"},
     *       @OA\Property(
     *         property="first_name",
     *        type="string"
     *     ),
     *    @OA\Property(
     *      property="last_name",
     *    type="string"
     * ),
     * @OA\Property(
     *  property="other_name",
     * type="string"
     * ),
     * @OA\Property(
     * property="email",
     * type="string"
     * ),
     * @OA\Property(
     * property="phone_number",
     * type="string"
     * 
     * ),
     * @OA\Property(
     * property="date_of_birth",
     * type="string"
     *  )
     * )
     * ),
     * @OA\Response(
     *   response=200,
     * description="successful operation",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean"
     * ),
     * @OA\Property(
     * property="message",
     * type="string"
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={"id":1,"first_name":"John","last_name":"Doe","other_name":"Doe","email":""
     * ,"phone_number":"08012345678","date_of_birth":"2021-04-11","admin_id":1,"created_at":"2021-04-11T11:12:12.000000Z","updated_at":"2021-04-11T11:12:12.000000Z"}
     * )
     * )
     * ),
     * @OA\Response(
     *  response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean"
     * ),
     * @OA\Property(
     * property="message",
     * type="string"
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={"message":"Unauthenticated."}
     * )
     * )
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean"
     * ),
     * @OA\Property(
     * property="message",
     * type="string"
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={"message":"You are not an admin"}
     * )
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean"
     * ),
     * @OA\Property(
     * property="message",
     * type="string"
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={"first_name":"The first name field is required.","last_name":"The last name field is required.","email":"The email field is required.","phone_number":"The phone number field is required."}
     * )
     * )
     * )
     * )
     * )
     * 
     * 
     * 
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'other_name' => 'nullable',
            'email' => 'required|email|unique:admin_information',
            'phone_number' => 'required|numeric',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 401);
        }

        if(Auth::user()->role != 'admin'){
            return $this->sendError('You are not an admin');
        }

        //convert date to laravel date
        
        $adminInformation = new AdminInformation();
        $adminInformation->first_name = $request->first_name;
        $adminInformation->last_name = $request->last_name;
        $adminInformation->other_name = $request->other_name;
        $adminInformation->email = $request->email;
        $adminInformation->phone_number = $request->phone_number;
        $adminInformation->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
        $adminInformation->admin_id = Auth::user()->id;
        $adminInformation->save();
        $adminInformation->refresh();

        return $this->sendResponse($adminInformation, 'Admin information created successfully.');
    }

    //create swagger annotation for the show method
    /**
     * 
     * @OA\Get(
     *     path="/api/admin/get-admin-information",
     *    tags={"Admin Information"},
     *    summary="Get admin information",
     *   description="Get admin information",
     *    operationId="showAdminInformation",
     * security={{"Bearer":{}}},
     *    @OA\Response(
     *        response=200,
     *       description="successful operation",
     *       @OA\JsonContent(
     *          @OA\Property(
     *             property="success",
     *           type="boolean"
     *         ),
     *         @OA\Property(
     *            property="message",
     *         type="string"
     *      ),
     *     @OA\Property(
     *       property="data",
     *    type="object",
     * example={"id":1,"first_name":"John","last_name":"Doe","other_name":"Doe","email":""
     * ,"phone_number":"08012345678","date_of_birth":"2021-04-11","admin_id":1,"created_at":"2021-04-11T10:05:05.000000Z","updated_at":"2021-04-11T10:05:05.000000Z"}
     * )
     * )
     * )
     * )
     * )
     * 
     * 
     * 
     */
    public function show()
    {
        
        $auth_admin = Auth::user();
        // if($auth_admin->role != 'admin'){
        //     return $this->sendError('You are not an admin');
        // }

        $adminInformation = AdminInformation::where('admin_id', $auth_admin->id)->first();
        
        return $this->sendResponse($adminInformation, 'Admin information retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    //create swagger annotation for the update method

    /**
     * 
     * @OA\Patch(
     *    path="/api/admin/update-admin-information/{id}",
     *   tags={"Admin Information"},
     *  summary="Update admin information",
     * description="Update admin information",
     * operationId="updateAdminInformation",
     * security={{"Bearer":{}}},
     * @OA\Parameter(
     *   name="id",
     * in="path",
     * description="Admin information id",
     * required=true,
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\RequestBody(
     *   required=true,
     * @OA\JsonContent(
     *  required={"first_name","last_name","email","phone_number"},
     * @OA\Property(
     * property="first_name",
     * type="string"
     * ),
     * @OA\Property(
     * property="last_name",
     * type="string"
     * ),
     * @OA\Property(
     * property="other_name",
     * type="string"
     * ),
     * @OA\Property(
     * property="email",
     * type="string"
     * ),
     * @OA\Property(
     * property="phone_number",
     * type="string"
     * ),
     * @OA\Property(
     * property="date_of_birth",
     * type="string"
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="successful operation",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean"
     * ),
     * @OA\Property(
     * property="message",
     * type="string"
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={"id":1,"first_name":"John","last_name":"Doe","other_name":"Doe","email":""
     * ,"phone_number":"08012345678","date_of_birth":"2021-04-11","admin_id":1,"created_at":"2021-04-11T10:05:05.000000Z","updated_at":"2021-04-11T10:05:05.000000Z"}
     * )
     * )
     * )
     * )
     * 
     */
    public function updateAdminInformation(Request $request, $id)
    {
        $admin_info_to_update = AdminInformation::find($id);

        if(!$admin_info_to_update){
            return $this->sendError('Admin information not found', 404);
        }
        if($admin_info_to_update->admin_id != Auth::user()->id){
            return $this->sendError('You are not authorized to update this information');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'other_name' => 'nullable',
            'email' => 'required|email|unique:admin_information,email,'.$id,
            'phone_number' => 'required|numeric',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 401);
        }

        if(Auth::user()->role != 'admin'){
            return $this->sendError('You are not an admin');
        }

        
        $admin_info_to_update->first_name = $request->first_name;
        $admin_info_to_update->last_name = $request->last_name;
        $admin_info_to_update->other_name = $request->other_name;
        $admin_info_to_update->email = $request->email;
        $admin_info_to_update->phone_number = $request->phone_number;
        $admin_info_to_update->date_of_birth = date('Y-m-d', strtotime($request->date_of_birth));
        $admin_info_to_update->admin_id = Auth::user()->id;
        $admin_info_to_update->save();
        $admin_info_to_update->refresh();

        return $this->sendResponse($admin_info_to_update, 'Admin information updated successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
