<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    // create swagger annotation for get user class level
    /**
     * @OA\Get(
     *     path="/api/user/get-auth-user-class-level",
     *    summary="Get user class level",
     * tags={"Users"},
     * security={{"Bearer":{}}},
     * description="Get user class level",
     * operationId="getUserClassLevel",
     * 
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="classLevel",
     * type="object",
     * example={
     * "id": 1,
     * "name": "Primary 1",
     *  "created_at": "2021-03-01T15:00:00.000000Z",
     * "updated_at": "2021-03-01T15:00:00.000000Z"
     * }
     * )
     * )
     * 
     * )
     * )
     * 
     * 
     * 
     */
    public function getAuthUserClassLevel()
    {
        $user = auth()->user();

        if($user->role=="admin"){
            return response()->json([
                'classLevel' => null,
                'message' => 'Admin has no class level'
            ]);
        }
        $user_class = $user->role == 'student' ? 
         Student::where('user_id', $user->id)->first()->class_level_id :
         Teacher::where('user_id', $user->id)->first()->class_level_id;
        $main_class = ClassLevel::where('id', $user_class)->first();
        return response()->json([
            'classLevel' => $main_class,
        ]);
    }
}
