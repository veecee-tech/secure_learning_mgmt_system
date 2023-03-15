<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Models\Student\Student;
use App\Services\TwilioSmsSender;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\StudentResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return StudentResource::collection(
            Student::all()
            ->sortByDesc('created_at')
        );

    }

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
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:students'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:students'],
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
            return $this->sendError('Validation Error.', $validator->errors());
        }


        //create new user with role student and auto generate password and auto generated username

        $username = $this->generateStudentId();

        //random password
        $random_password = rand(100000, 999999);

        $user = User::create([
            'username' => $username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($random_password),
        ]);
        
        $user->role = 'student';
        $user->save();


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
            'enabled' => true
        ]);

        //send sms to student
        $this->twilioSmsSender->sendOTP(
            $request->phone_number,
            "Your username is $username and password is $random_password"
        );

        $success = [
            'username' => $username,
            'password' => $random_password,
            'student' => $student,
        ];

        return $this->sendResponse($success, 'Student created successfully.');

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

        $student = User::with(
            'student'
        )
        ->where('id', $id)
        ->first();

        $success['id'] = $student->student->id;
        $success['student_id'] = $student->username;
        $success['fullname'] = $student->student->getFullnameAttribute();
        $success['class'] = $student->student->classLevel->name;

        return $this->sendResponse($success, 'Student retrieved successfully.');

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

        $student = Student::find($id);
        $studentToDelete = User::find($student->user_id);

        $deleted = $studentToDelete->delete();

        return $deleted ? 
            $this->sendResponse($deleted, 'Student deleted successfully.') : 
            $this->sendError('Student not deleted ecause it is dependent on other model.');
    }

    public function downloadStudentList()
    {
        $students = $this->index();

        $filename = 'student_list.csv';
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Student ID', 'Fullname', 'Class'));

        foreach($students as $student) {
            fputcsv($handle, array($student->user->username, $student->getFullnameAttribute(), $student->classLevel()->first()->name));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download($filename, 'student_list.csv', $headers);
    }
}
