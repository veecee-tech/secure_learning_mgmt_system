<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Models\Teacher\Teacher;
use App\Services\TwilioSmsSender;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TeacherResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

/**
 * @OA\Post(
 * path="/api/admin/create-teacher",
 * summary="Create a new teacher",
 * description="Create a new teacher",
 * operationId="createTeacher",
 * tags={"Teachers"},
 * security={{"Bearer":{}}},
 * @OA\RequestBody(
 *   required=true,
 *  description="Pass teacher details",
 * @OA\JsonContent(
 * required={"first_name","last_name","phone_number","date_of_birth","enrollment_status","class_level_id"},
 * @OA\Property(property="first_name", type="string", example="John"),
 * @OA\Property(property="last_name", type="string", example="Doe"),
 * @OA\Property(property="other_name", type="string", example="Doe"),
 * @OA\Property(property="email", type="string", example="",),
 * @OA\Property(property="phone_number", type="string", example="08012345678"),
 * @OA\Property(property="date_of_birth", type="string", example="2021-03-01"),
 * @OA\Property(property="enrollment_status", type="string", example="active"),
 * @OA\Property(property="class_level_id", type="integer", example=1),
 * @OA\Property(property="parent_first_name", type="string", example="John"),
 * @OA\Property(property="parent_last_name", type="string", example="Doe"),
 * @OA\Property(
 *                 property="parent_phone_number_1",
 *                 type="string",
 *                 example="08012345678"
 *             ),
 *             @OA\Property(
 *                 property="parent_phone_number_2",
 *                 type="string",
 *                 example="08012345678"
 *             ),
 *             @OA\Property(
 *                 property="parent_home_address",
 *                 type="string",
 *                 example="No 1, John Doe Street, Lagos"
 *             ),
 *             @OA\Property(
 *                 property="parent_emergency_contact",
 *                 type="string",
 *                 example="08012345678"
 *             ),
 *         )
 *     ),
 * @OA\Response(
 *   response=201,
 * description="Created",
 * @OA\JsonContent(
 *   @OA\Property(property="data", type="object",
 * example={
 * {
 * "id": 1,
 * "teacher_id": "T000001",
 * "first_name": "John",
 * "last_name": "Doe",
 * "other_name": "Doe",
 * "email": "",
 * "phone_number": "08012345678",
 * "class_level_id": 1,
 * "class_level": {
 * "id": 1,
 * "name": "JSS 1"
 * },
 * "created_at": "2021-03-01T12:00:00.000000Z",
 * "updated_at": "2021-03-01T12:00:00.000000Z"
 * 
 * 
 * }
 * },
 * 
 * )
 * ),
 * @OA\Response(
 *  response=422,
 * description="Unprocessable Entity",
 * @OA\JsonContent(
 *  @OA\Property(property="message", type="string", example="The given data was invalid."),
 * @OA\Property(property="errors", type="object",
 * example={
 * {
 * "first_name": {
 * "The first name field is required."
 * }
 * },
 * {
 * "last_name": {
 * "The last name field is required."
 * }
 * },
 * {
 * "phone_number": {
 * "The phone number field is required."
 * }
 * },
 * {
 * "date_of_birth": {
 * "The date of birth field is required."
 * }
 * },
 * {
 * "enrollment_status": {
 * "The enrollment status field is required."
 * }
 * },
 * {
 * "class_level_id": {
 * "The class level id field is required."
 * }
 * }
 * }
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


/**
 * @OA\Patch(
 *    path="/api/admin/teachers/update/{id}",
 *  summary="Update teacher",
 * tags={"Teachers"},
 * security={{"Bearer":{}}},
 * description="Update teacher",
 * operationId="updateTeacher",
 * @OA\Parameter(
 *   name="id",
 *  in="path",
 * description="teacher id",
 * required=true,
 * @OA\Schema(
 * type="integer",
 * format="int64"
 * )
 * ),
 * @OA\RequestBody(
 *  required=true,
 * description="Pass teacher details",
 * @OA\JsonContent(
 * required={
 * "first_name",
 * "last_name",
 * "phone_number",
 * "date_of_birth",
 * "enrollment_status",
 * "class_level_id"
 * },
 * @OA\Property(
 * property="first_name",
 * type="string",
 * example="John"
 * ),
 * @OA\Property(
 * property="last_name",
 * type="string",
 * example="Doe"
 * ),
 * @OA\Property(
 * property="other_name",
 * type="string",
 * example="Smith"
 * ),
 * @OA\Property(
 * property="email",
 * type="string",
 * example=""
 * ),
 * @OA\Property(
 * property="phone_number",
 * type="string",
 * example="08012345678"
 * ),
 * @OA\Property(
 * property="date_of_birth",
 * type="string",
 * format="date",
 * example="2021-01-01"
 * ),
 * @OA\Property(
 * property="enrollment_status",
 * type="string",
 * enum={"New", "Old"},
 * example="New"
 * ),
 * @OA\Property(
 * property="class_level_id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="parent_first_name",
 * type="string",
 * example="Jane"
 * ),
 *  
 * @OA\Property(
 * property="parent_last_name",
 * type="string",
 * example="Doe"
 * ),
 * @OA\Property(
 * property="parent_phone_number_1",
 * type="string",
 * example="08012345678"
 * ),
 * @OA\Property(
 * property="parent_phone_number_2",
 * type="string",
 *  
 * example="08012345678"
 * ),
 * @OA\Property(
 * property="parent_home_address",
 * type="string",
 * example="No 1, John Doe Street, Lagos"
 * ),
 * @OA\Property(
 * property="parent_emergency_contact",
 * type="string",
 * example="08012345678"
 * ),
 * )
 * ),
 *     @OA\Response(
 *         response=200,
 *         description="Created",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Teacher updated successfully"
 *             ),
 *             @OA\Property(
 *                 property="teacher",
 *                 type="object",
 *                  example={
 *                     "id": 1,
 *                    "first_name": "John",
 *                   "last_name": "Doe",
 *                 "other_name": "Smith",
 *              "email": null,
 *          "phone_number": "08012345678",
 *      "date_of_birth": "2021-01-01",
 * "enrollment_status": "New",
 * "class_level_id": 1,
 * "created_at": "2021-01-01T00:00:00.000000Z",
 * "updated_at": "2021-01-01T00:00:00.000000Z"
 * }
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Bad Request",
 * @OA\JsonContent(
 * @OA\Property(
 * property="message",
 * type="string",
 * example="The given data was invalid."
 * ),
 * @OA\Property(
 * property="errors",
 * type="object",
 * example="Invalid data"
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 */
class TeacherController extends BaseController
{
    //

    private $twilioSmsSender;

    public function __construct(TwilioSmsSender $twilioSmsSender)
    {
        $this->twilioSmsSender = $twilioSmsSender;
    }

    public function generateTeacherId()
    {
        $lastId = DB::table('users')->latest('id')->first();
        if ($lastId) {
            $teacherId = "T" . str_pad($lastId->id + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $teacherId = "T000001";
        }

        return $teacherId;
    }

    // generate annotation for index method
    /**
     * @OA\Get(
     * 
     * path="/api/admin/teachers",
     * summary="Get list of teachers",
     * description="Get list of teachers",
     * operationId="getTeachers",
     * tags={"Teachers"},
     * security={{"Bearer":{}}},
     * @OA\Response(
     *   response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",  
     * type="boolean",
     * example=true
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={
     * 
     * {
     * "id": 1,
     * "teacher_id": "m000001",
     * "first_name": "John",
     * "last_name": "Doe",
     * "other_name": "Doe",
     * "email": "",
     * "phone_number": "08012345678",
     * "date_of_birth": "2021-01-01",
     * "class_level_id": 1,
     * "parent_id": 1,
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z",
     * "class_level": {
     * "id": 1,
     * "name": "Primary 1",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * 
     * 
     * }
     * }
     * )
     * )
     * 
     * ))))
     *
     *      
     * 
     */
    public function index()
    {
        //
        return TeacherResource::collection(
            Teacher::all()
                ->sortByDesc('created_at')
        );
    }

    /**
     * 
     * @OA\Get(
     * 
     * path="/api/admin/teachers/get-class-level-list",
     * summary="Get list of class levels",
     * description="Get list of class levels",
     * operationId="getClassLevelListeachers",
     * tags={"Teachers"},
     * security={{"Bearer":{}}},
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="success",
     * type="boolean",
     * example=true
     * ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={
     * {
     * "id": 1,
     * "name": "Primary 1",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * {
     * "id": 8,
     * "name": "Primary 2",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * {
     * "id": 3,
     * "name": "Primary 3",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * {
     * "id": 9,
     * "name": "Primary 4",
     * "created_at": "2021-01-01T00:00:00.000000Z",
     * "updated_at": "2021-01-01T00:00:00.000000Z"
     * },
     * 
     * }
     * 
     * )
     * 
     * )
     * 
     * )
     * 
     * ),
     * 
     * @OA\Response(
     * response=422,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Unauthenticated User."
     * )
     * )
     * )
     * 
     * 
     * 
     * 
     */

    public function getClassLevelList()
    {
        $classLevelList = ClassLevel::all();
        return $classLevelList;
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

        $validatedData = Validator::make(
            $request->all(),
            [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'other_name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:teachers'],
                'phone_number' => ['required', 'string', 'max:255', 'unique:teachers', 'unique:users'],
                'date_of_birth' => ['required', 'date'],
                'enrollment_status' => ['required', 'numeric'],
                'class_level_id' => ['required'],
                'parent_first_name' => ['nullable', 'string', 'max:255'],
                'parent_last_name' => ['nullable', 'string', 'max:255'],
                'parent_phone_number_1' => ['nullable', 'string', 'max:255'],
                'parent_phone_number_2' => ['nullable', 'string', 'max:255'],
                'parent_home_address' => ['nullable', 'string', 'max:255'],
                'parent_emergency_contact' => ['nullable', 'string', 'max:255'],


            ],
            $message = [
                'first_name.required' => 'First name is required',
                'last_name.required' => 'Last name is required',
                'phone_number.required' => 'Phone number is required',
                'date_of_birth.required' => 'Date of birth is required',
                'enrollment_status.required' => 'Enrollment status is required',
                'class_level_id.required' => 'Class level is required',
            ]

        );

        if ($validatedData->fails()) {
            return $this->sendError('Validation Error.', $validatedData->errors(), 422);
        }

        $username = $this->generateTeacherId();

        //random password
        $random_password = rand(100000, 999999);

        $user = User::create([
            'username' => $username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($random_password),
        ]);

        $user->role = 'teacher';
        $user->save();

        // $teacher = $user->teacher()->create([
        //     'first_name' => $request->first_name,
        //     'last_name' => $request->last_name,
        //     'other_name' => $request->other_name,
        //     'email' => $request->email,
        //     'phone_number' => $request->phone_number,
        //     'date_of_birth' => $request->date_of_birth,
        //     'enrollment_status' => $request->enrollment_status,
        //     'class_level_id' => $request->class_level_id,
        //     'parent_first_name' => $request->parent_first_name,
        //     'parent_last_name' => $request->parent_last_name,
        //     'parent_phone_number_1' => $request->parent_phone_number_1,
        //     'parent_phone_number_2' => $request->parent_phone_number_2,
        //     'parent_home_address' => $request->parent_home_address,
        //     'parent_emergency_contact' => $request->parent_emergency_contact,
        // ]);

        // $teacher->classLevel()->attach($request->class_level_id);
        // $teacher->save();

        $teacher = new Teacher();
        $teacher->user_id = $user->id;
        $teacher->first_name = $request->first_name;
        $teacher->last_name = $request->last_name;
        $teacher->other_name = $request->other_name;
        $teacher->email = $request->email;
        $teacher->phone_number = $request->phone_number;
        $teacher->date_of_birth = $request->date_of_birth;
        $teacher->enrollment_status = $request->enrollment_status;
        $teacher->class_level_id = $request->class_level_id;
        $teacher->parent_first_name = $request->parent_first_name;
        $teacher->parent_last_name = $request->parent_last_name;
        $teacher->parent_phone_number_1 = $request->parent_phone_number_1;
        $teacher->parent_phone_number_2 = $request->parent_phone_number_2;
        $teacher->parent_home_address = $request->parent_home_address;
        $teacher->parent_emergency_contact = $request->parent_emergency_contact;
        $teacher->save();

        //create two step verification
        $user->twoStepVerification()->create([
            'user_id' => $user->id,
            'enabled' => true
        ]);

        //send sms to teacher
        // $this->twilioSmsSender->sendOTP(
        //     $request->phone_number,
        //     "Your username is $username and password is $random_password"
        // );

        $success = [
            'username' => $username,
            'password' => $random_password,
            'teacher' => $teacher,
        ];

        return $this->sendResponse($success, 'Teacher created successfully.', 201);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/teachers/view/{id}",
     *    summary="Get teacher by id",
     *    tags={"Teachers"},
     * security={{"Bearer":{}}},
     *   description="Get teacher by id",
     *  operationId="showTeacher",
     *    @OA\Parameter(
     *        name="id",
     *       in="path",
     *      required=true,
     *     @OA\Schema(
     *         type="integer"
     *    )
     * ),
     * @OA\Response(
     *   response=200,
     *  description="Successful operation",
     * @OA\JsonContent(
     *     @OA\Property(property="success", type="boolean"),
     *    @OA\Property(property="message", type="string"),
     *  @OA\Property(
     *     property="data",
     *   type="object",
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="teacher_id", type="string"),
     * @OA\Property(property="fullname", type="string"),
     * @OA\Property(property="class", type="string"),
     * )
     * )
     * ),
     * @OA\Response(
     *  response=404,
     * description="Not found"
     * )
     *  
     * 
     * 
     * )
     * )
     * )
     */
    public function show($id)
    {

        return TeacherResource::collection(
            Teacher::where('id', $id)->get()
        );

        // return $this->sendResponse($success, 'Teacher retrieved successfully.', 200);
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

        $validator = Validator::make($request->all(), [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'enrollment_status' => ['nullable'],
            'class_level_id' => ['nullable'],
            'parent_first_name' => ['nullable', 'string', 'max:255'],
            'parent_last_name' => ['nullable', 'string', 'max:255'],
            'parent_phone_number_1' => ['nullable', 'string', 'max:255'],
            'parent_phone_number_2' => ['nullable', 'string', 'max:255'],
            'parent_home_address' => ['nullable', 'string', 'max:255'],
            'parent_emergency_contact' => ['nullable', 'string', 'max:255'],

        ]);

        $class_name = $request->class;
        $class_level = ClassLevel::where('name', $class_name)->first();

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }
        
        $teacher_to_update = Teacher::find($id);
        $teacher_to_update->first_name = $request->first_name;
        $teacher_to_update->last_name = $request->last_name;
        $teacher_to_update->other_name = $request->other_name;
        $teacher_to_update->email = $request->email;
        $teacher_to_update->phone_number = $request->phone_number;
        $teacher_to_update->date_of_birth = $request->date_of_birth;
        $teacher_to_update->enrollment_status = $request->enrollment_status;
        $teacher_to_update->class_level_id = $class_level->id;
        $teacher_to_update->parent_first_name = $request->parent_first_name;
        $teacher_to_update->parent_last_name = $request->parent_last_name;
        $teacher_to_update->parent_phone_number_1 = $request->parent_phone_number_1;
        $teacher_to_update->parent_phone_number_2 = $request->parent_phone_number_2;
        $teacher_to_update->parent_home_address = $request->parent_home_address;
        $teacher_to_update->parent_emergency_contact = $request->parent_emergency_contact;
        $teacher_to_update->save();
        $teacher_to_update->refresh();

        $success = [
            'teacher' => $teacher_to_update,
        ];

        return $this->sendResponse($success, 'Teacher updated successfully.', 200);

    }


    /**
     * @OA\Delete(
     *    path="/api/admin/teachers/delete/{id}",
     *   summary="Delete teacher by id",
     * tags={"Teachers"},
     * security={{"Bearer":{}}},
     * description="Delete teacher by id",
     * operationId="deleteTeacher",
     * @OA\Parameter(
     *       name="id",
     *     in="path",
     *   required=true,
     * @OA\Schema(
     *      type="integer"
     * )
     * ),
     * 
     * @OA\Response(
     *   response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     *   @OA\Property(property="success", type="boolean"),
     * @OA\Property(property="message", type="string"),
     * @OA\Property(
     *     property="data",
     *  type="object",
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="teacher_id", type="string"),
     * @OA\Property(property="fullname", type="string"),
     * @OA\Property(property="class", type="string"),
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not found"
     * )
     * )
     * )
     * )
     * )
     * )
     * )
     */
    public function destroy($id)
    {
        $teacher = Teacher::find($id);
        $teacherToDelete = User::find($teacher->user_id);

        $deleted = $teacherToDelete->delete();

        return $deleted ?
            $this->sendResponse($deleted, 'Teacher deleted successfully.', 200) :
            $this->sendError('Error','Teacher not deleted ecause it is dependent on other model.'. 400);
    }

    //generate annotation for the downloadTeacherList method
    /**
     * @OA\Get(
     *    path="/api/admin/teachers/download",
     *   summary="Download teacher list",
     * tags={"Teachers"},
     * security={{"Bearer":{}}},
     * description="Download teacher list",
     * operationId="downloadTeacherList",
     * @OA\Response(
     *   response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     *   @OA\Property(property="success", type="boolean"),
     * @OA\Property(property="message", type="string"),
     * @OA\Property(
     *     property="data",
     *  type="object",
     * @OA\Property(property="id", type="integer"),
     * @OA\Property(property="teacher_id", type="string"),
     * @OA\Property(property="fullname", type="string"),
     * @OA\Property(property="class", type="string"),
     * )
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not found"
     * )
     * )
     * )
     * )
     * )
     * )
     * )
     */

    public function downloadTeacherList()
    {
        $teachers = $this->index();

        $filename = "teachers.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Teacher ID', 'Fullname', 'Class'));

        foreach ($teachers as $teacher) {
            fputcsv($handle, array($teacher->user->username, $teacher->getFullnameAttribute(), $teacher->classLevel()->first()->name));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download($filename, 'teachers.csv', $headers);
    }

    public function getLoggedInTeacher()
    {
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();
        $success = [
            'teacher' => $teacher,
        ];

        return $this->sendResponse($success, 'Teacher retrieved successfully.', 200);
    }
}
