<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Models\Teacher\Teacher;
use App\Services\TwilioSmsSender;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TeacherResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

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



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index()
    {
        //
        return TeacherResource::collection(
            Teacher::all()
            ->sortByDesc('created_at')
        );
    }

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
        
        $validatedData = Validator::make($request->all(), [
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
            return $this->sendError('Validation Error.', $validatedData->errors());
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

         //send sms to student
         $this->twilioSmsSender->sendOTP(
            $request->phone_number,
            "Your username is $username and password is $random_password"
        );

        $success = [
            'username' => $username,
            'password' => $random_password,
            'teacher' => $teacher,
        ];

        return $this->sendResponse($success, 'Teacher created successfully.');

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $teacher = User::with(
            'teacher'
        )
        ->where('id', $id)
        ->first();        
        $success['id'] = $teacher->teacher->id;
        $success['teacher_id'] = $teacher->username;
        $success['fullname'] = $teacher->teacher->getFullnameAttribute();
        $success['class'] = $teacher->teacher->classLevel()->first()->name;

        return $this->sendResponse($success, 'Teacher retrieved successfully.');
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
        $teacher = Teacher::find($id);
        $teacherToDelete = User::find($teacher->user_id);

        $deleted = $teacherToDelete->delete();

        return $deleted ? 
            $this->sendResponse($deleted, 'Teacher deleted successfully.') : 
            $this->sendError('Teacher not deleted ecause it is dependent on other model.');
    }

    public function downloadTeacherList()
    {
        $teachers = $this->index();

        $filename = "teachers.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('Teacher ID', 'Fullname', 'Class'));

        foreach($teachers as $teacher) {
            fputcsv($handle, array($teacher->user->username, $teacher->getFullnameAttribute(), $teacher->classLevel()->first()->name));
        }

        fclose($handle);

        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return response()->download($filename, 'teachers.csv', $headers);
    }
}
