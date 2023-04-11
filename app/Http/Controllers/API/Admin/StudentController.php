<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Models\Student\Student;
use App\Services\TwilioSmsSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use Haruncpi\LaravelUserActivity\Models\Log;


/**
 * @OA\Post(
 *     path="/api/admin/create-student",
 *     summary="Create a new student",
 *     tags={"Students"},
 * security={{"Bearer":{}}},
 *     description="Create a new student",
 *     operationId="createStudent",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Pass student details",
 *         @OA\JsonContent(
 *             required={
 *                 "first_name",
 *                 "last_name",
 *                 "phone_number",
 *                 "date_of_birth",
 *                 "enrollment_status",
 *                 "class_level_id"
 *             },
 *             @OA\Property(
 *                 property="first_name",
 *                 type="string",
 *                 example="John"
 *             ),
 *             @OA\Property(
 *                 property="last_name",
 *                 type="string",
 *                 example="Doe"
 *             ),
 *             @OA\Property(
 *                 property="other_name",
 *                 type="string",
 *                 example="Smith"
 *             ),
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 example=""
 *             ),
 *             @OA\Property(
 *                 property="phone_number",
 *                 type="string",
 *                 example="08012345678"
 *             ),
 *             @OA\Property(
 *                 property="date_of_birth",
 *                 type="string",
 *                 format="date",
 *                 example="2021-01-01"
 *             ),
 *             @OA\Property(
 *                 property="enrollment_status",
 *                 type="string",
 *                 enum={"New", "Enrolled"},
 *                 example="New"
 *             ),
 *             @OA\Property(
 *                 property="class_level_id",
 *                 type="integer",
 *                 example=1
 *             ),
 *             @OA\Property(
 *                 property="parent_first_name",
 *                 type="string",
 *                 example="Jane"
 *             ),
 *             @OA\Property(
 *                 property="parent_last_name",
 *                 type="string",
 *                 example="Doe"
 *             ),
 *             @OA\Property(
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
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Student created successfully"
 *             ),
 *             @OA\Property(
 *                 property="student",
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


//create update student annotation

/**
 * @OA\Patch(
 *    path="/api/admin/students/update/{id}",
 *  summary="Update student",
 * tags={"Students"},
 * security={{"Bearer":{}}},
 * description="Update student",
 * operationId="updateStudent",
 * @OA\Parameter(
 *   name="id",
 *  in="path",
 * description="Student id",
 * required=true,
 * @OA\Schema(
 * type="integer",
 * format="int64"
 * )
 * ),
 * @OA\RequestBody(
 *  required=true,
 * description="Pass student details",
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
 * enum={"New", "Enrolled"},
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
 *                 example="Student updated successfully"
 *             ),
 *             @OA\Property(
 *                 property="student",
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
class StudentController extends BaseController
{

    private $twilioSmsSender;

    public function __construct(TwilioSmsSender $twilioSmsSender)
    {
        $this->twilioSmsSender = $twilioSmsSender;
    }

    public function generateStudentId()
    {
        $lastId = DB::table('users')->latest('id')->first();
        if ($lastId) {
            $studentId = "m" . str_pad($lastId->id + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $studentId = "m000001";
        }

        return $studentId;
    }


    //generate annotation for index method

    /**
     * @OA\Get(
     *    path="/api/admin/students",
     *   summary="Get list of students",
     * 
     *  tags={"Students"},
     * security={{"Bearer":{}}},
     * description="Get list of students",
     * 
     * operationId="getStudents",
     *
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
     * "student_id": "m000001",
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

        return StudentResource::collection(
            Student::all()
                ->sortByDesc('created_at')
        );
    }

    //generate annotation for getClassLevelList method
    /**
     * @OA\Get(
     * 
     * path="/api/admin/students/get-class-level-list",
     * summary="Get list of class levels",
     *  
     * tags={"Students"},
     * security={{"Bearer":{}}},
     * description="Get list of class levels",
     * 
     * operationId="getClassLevelList",
     * 
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
     * "id": 2,
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
     * "id": 4,
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
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Unauthenticated."
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:students'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:students', 'unique:users'],
            'date_of_birth' => ['required', 'date'],
            'enrollment_status' => ['required'],
            'class_level_id' => ['required'],
            'parent_first_name' => ['nullable', 'string', 'max:255'],
            'parent_last_name' => ['nullable', 'string', 'max:255'],
            'parent_phone_number_1' => ['nullable', 'string', 'max:255'],
            'parent_phone_number_2' => ['nullable', 'string', 'max:255'],
            'parent_home_address' => ['nullable', 'string', 'max:255'],
            'parent_emergency_contact' => ['nullable', 'string', 'max:255'],

        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }


        //create new user with role student and auto generate password and auto generated username

        $username = $this->generateStudentId();

        //random password
        $random_password = rand(100000, 999999);

        // $user = User::create([
        //     'username' => $username,
        //     'phone_number' => $request->phone_number,
        //     'password' => Hash::make($random_password),
        // ]);

        $user = new User;
        $user->username = $username;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($random_password);
        $user->role = 'student';
        

        if($user->save()){
            $student = new Student;
            $student->user_id = $user->id;
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->other_name = $request->other_name;
            $student->email = $request->email;
            $student->phone_number = $request->phone_number;
            $student->date_of_birth = $request->date_of_birth;
            $student->enrollment_status = $request->enrollment_status;
            $student->class_level_id = $request->class_level_id;
            $student->parent_first_name = $request->parent_first_name;
            $student->parent_last_name = $request->parent_last_name;
            $student->parent_phone_number_1 = $request->parent_phone_number_1;
            $student->parent_phone_number_2 = $request->parent_phone_number_2;
            $student->parent_home_address = $request->parent_home_address;
            $student->parent_emergency_contact = $request->parent_emergency_contact;
            $student->save();

            //create two step verification
            $user->twoStepVerification()->create([
                'user_id' => $user->id,
                'enabled' => false
            ]);

            $success = [
                'username' => $username,
                'password' => $random_password,
                'student' => $student,
            ];
    
            return $this->sendResponse($success, 'Student created successfully.', 201);
        }
        else{
            return $this->sendError('Error creating student', [], 400);
        }
        


        

        //send sms to student
        

        // $this->twilioSmsSender->sendOTP(
        //     $request->phone_number,
        //     "Your username is $username and password is $random_password"
        // ) ? [] : $this->sendError('Error sending sms', [], 400);
        
        //remove record if sms fails
        // if (!$this->twilioSmsSender->sendOTP(
        //     $request->phone_number,
        //     "Your username is $username and password is $random_password"
        // )) {
        //     $user->delete();
        //     $student->delete();

        //     return $this->sendError('Error sending sms', [], 400);
        // }

        // $success = [
        //     'username' => $username,
        //     'password' => $random_password,
        //     'student' => $student,
        // ];

        // return $this->sendResponse($success, 'Student created successfully.', 201);
    }

    //generate annotation for the show() method (GET /students/{id})
    /**
     * @OA\Get(
     * path="/api/admin/students/view/{id}",
     * summary="View student",
     * description="View student",
     * operationId="viewStudent",
     * tags={"Students"},
     * security={{"Bearer":{}}},
     * 
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Student ID",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * 
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="success",
     * type="object")
     * 
     * )
     * )
     * 
     * )
     * 
     * )
     */
    public function show($id)
    {
        //

        $student = User::with(
            'student'
        )
            ->where('id', $id)
            ->first();

        $success['id'] = $student->student;
        $success['student_id'] = $student->username;
        $success['fullname'] = $student->student->getFullnameAttribute();
        $success['class'] = $student->student->classLevel;

        return $this->sendResponse($success, 'Student retrieved successfully.', 200);
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

        $validator = Validator::make($request->all(), [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'other_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:students'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:students', 'unique:users'],
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

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }

        $student = Student::find($id);
        $student->first_name = $request->first_name;
        $student->last_name = $request->last_name;
        $student->other_name = $request->other_name;
        $student->email = $request->email;
        $student->phone_number = $request->phone_number;
        $student->date_of_birth = $request->date_of_birth;
        $student->enrollment_status = $request->enrollment_status;
        $student->class_level_id = $request->class_level_id;
        $student->parent_first_name = $request->parent_first_name;
        $student->parent_last_name = $request->parent_last_name;
        $student->parent_phone_number_1 = $request->parent_phone_number_1;
        $student->parent_phone_number_2 = $request->parent_phone_number_2;
        $student->parent_home_address = $request->parent_home_address;
        $student->parent_emergency_contact = $request->parent_emergency_contact;
        $student->save();
        $student->refresh();

        $success = [
            'student' => $student,
        ];

        return $this->sendResponse($success, 'Student updated successfully.', 200);
    }

    /**
     * @OA\Delete(
     * path="/api/admin/students/delete/{id}",
     * summary="Delete student",
     * description="Delete student",
     * operationId="deleteStudent",
     * tags={"Students"},
     * security={{"Bearer":{}}},
     * 
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Student ID",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * format="int64"
     * )
     * ),
     * 
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="success",
     * type="object")
     * 
     * )
     * )
     * 
     * )
     * 
     * )
     */
    public function destroy($id)
    {
        //

        $student = Student::find($id);

        if(is_null($student)){
            return $this->sendError('Student not found.',[], 400);
        };
        $studentToDelete = User::find($student->user_id);

        $deleted = $studentToDelete->delete();

        return $deleted ?
            $this->sendResponse($deleted, 'Student deleted successfully.', 200) :
            $this->sendError('Error','Student not deleted ecause it is dependent on other model.', 400);
    }


    //generate annotation for the downloadStudentList() method (GET /students/download)
    /**
     * @OA\Get(
     * path="/api/admin/students/download",
     * summary="Download student list",
     * description="Download student list",
     * operationId="downloadStudentList",
     * tags={"Students"},
     * security={{"Bearer":{}}},
     * 
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * 
     * @OA\Property(
     * property="success",
     * type="object")
     * 
     * )
     * )
     * 
     * )
     * 
     * )
     */

    public function downloadStudentList()
    {
        $students = $this->index();

        $filename = 'student_list.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Student ID', 'Fullname', 'Class'));

        foreach ($students as $student) {
            fputcsv($handle, array($student->user->username, $student->getFullnameAttribute(), $student->classLevel()->first()->name));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download($filename, 'student_list.csv', $headers);
    }

//generate annotation for the searchStudentByClassAndName() method (GET /students/search-by-class-level-and-name)
/**
 * @OA\Get(
 * path="/api/admin/students/search-by-class-level-and-name",
 * summary="Search student by class level and name",
 * description="Search student by class level and name",
 * operationId="searchStudentByClassAndName",
 * tags={"Students"},
 * security={{"Bearer":{}}},
 * 
 * @OA\Parameter(
 * name="class_level_id",
 * in="query",
 * description="Class level ID",
 * required=true,
 * @OA\Schema(
 * type="integer",
 * format="int64"
 * )
 * ),
 * 
 * @OA\Parameter(
 * name="search_term",
 * in="query",
 * description="Search term",
 * required=true,
 * @OA\Schema(
 * type="string"
 * ),
 * ),
 * 
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\JsonContent(
 * 
 * @OA\Property(
 * property="success",
 * type="object")
 * 
 * )
 * )
 * 
 * )
 * 
 */ 
    public function searchStudentByClassAndName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_level_id' => ['required'],
            'search_term' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }

        $students = Student::where('class_level_id', $request->class_level_id)
            ->where(function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search_term . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search_term . '%')
                    ->orWhere('other_name', 'like', '%' . $request->search_term . '%');
            })
            ->get();
        
        if(count($students) == 0) {
            return $this->sendError('Record Not Found', [], 404);
        }

        return $this->sendResponse($students, 'Students retrieved successfully.', 200);
    }


//generate annotation for userLoggedActivity() method (GET /user-logged-activity)
/**
 * @OA\Get(
 * path="/api/admin/user-logged-activity",
 * summary="Get user logged activity",
 * description="Get user logged activity",
 * operationId="userLoggedActivity",
 * tags={"Users"},
 * security={{"Bearer":{}}},
 * 
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\JsonContent(
 * 
 * @OA\Property(
 * property="success",
 * type="object")
 * 
 * )
 * )
 *  )
 * )
 * 
 */ 

    public function userLoggedActivity(){
        $logs = Log::all();
        return $this->sendResponse($logs, 'User activity retrieved successfully.', 200);
    }

    public function getLoggedInStudent(){
        $student = Student::where('user_id', Auth::user()->id)->first();
        return $this->sendResponse($student, 'Student retrieved successfully.', 200);
    }
}
