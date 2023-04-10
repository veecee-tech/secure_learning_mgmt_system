<?php

use Illuminate\Http\Request;
use Twilio\TwiML\Video\Room;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Admin\StudentController;
use App\Http\Controllers\API\Admin\TeacherController;
use App\Http\Controllers\Security\SecurityController;
use App\Http\Controllers\API\ProfilePictureController;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\CurriculumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// group route for auth
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/send-password-reset-otp', [AuthController::class, 'sendPasswordResetOtp']);
    Route::post('/verify-password-reset-otp', [AuthController::class, 'verifyPasswordResetOtp']);
    Route::post('/reset-password-after-otp-confirmation', [AuthController::class, 'resetPasswordAfterOtpConfirmation']);

});

Route::get('/users', function () {
    $users = App\Models\User::all();
    return response()->json($users);
});

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    //create route for admin
    Route::group(['prefix' => 'admin'], function () {
        
        Route::get('/dashboard/total-students', [DashboardController::class, 'getTotalStudents']);
        Route::get('/dashboard/total-teachers', [DashboardController::class, 'getTotalTeachers']);

        
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/create-student', [StudentController::class, 'store']);
        Route::get('/students/view/{id}', [StudentController::class, 'show']);
        Route::patch('/students/update/{id?}', [StudentController::class, 'update']);
        Route::delete('/students/delete/{id?}', [StudentController::class, 'destroy']);
        //download student list
        Route::get('/students/download', [StudentController::class, 'downloadStudentList']);
        Route::get('/students/get-class-level-list', [StudentController::class, 'getClassLevelList']);
        //search student by class level
        Route::get('/students/search-by-class-level-and-name', [StudentController::class, 'searchStudentByClassAndName']);
        //user logged activity
        Route::get('/user-logged-activity', [StudentController::class, 'userLoggedActivity']);

       
        Route::post('/create-teacher', [TeacherController::class, 'store']);
        Route::get('/teachers/view/{id}', [TeacherController::class, 'show']);
        Route::patch('/teachers/update/{id}', [TeacherController::class, 'update']);
        Route::delete('/teachers/delete/{id}', [TeacherController::class, 'destroy']);
        //download teacher list
        Route::get('/teachers/download', [TeacherController::class, 'downloadTeacherList']);
        Route::get('/teachers/get-class-level-list', [TeacherController::class, 'getClassLevelList']);
        Route::get('/teachers', [TeacherController::class, 'index']);

        
        Route::post('/subject/store', [CurriculumController::class, 'store']);
        Route::get('class/all', [CurriculumController::class, 'allClassLevels']);
        Route::get('class/{id}', [CurriculumController::class, 'getSingleClass']);
        Route::get('class/{id?}/subjects', [CurriculumController::class, 'getSubjectsByClass']);
        Route::get('class/{id}/subject/{subject_id}', [CurriculumController::class, 'getSingleSubject']);
        //createSingleTopic
        Route::post('/topic/create-single-topic', [CurriculumController::class, 'createSingleTopic']);


        Route::get('subjects/all', [CurriculumController::class, 'allSubjects']);

    });

    //create route for teacher
    Route::group(['prefix' => 'teacher', 'namespace' => 'Teacher'], function () {
    
    });

    Route::group(['prefix' => 'security', 'namespace' => 'Security'], function () {
        Route::post('/change-password', [SecurityController::class, 'changePassword']);
        Route::post('/two-step-verification', [SecurityController::class, 'setTwoStepVerification']);
        //get current two step authentication
        Route::get('/get/two-step-verification-details', [SecurityController::class, 'getCurrentTwoStepVerification']);
    });

    Route::group(['prefix' => 'student', 'namespace' => 'Student'], function () {
        
        //get single topic
        Route::get('/read/topic/{id}', [CurriculumController::class, 'readTopic']);
        //get all topics
        Route::get('/read/subject/{subject_id}/topics', [CurriculumController::class, 'getAllTopicsBySubject']);
    });

    //create route group for profile photo
    Route::group(['prefix' => 'profile-photo'], function () {
        Route::post('/upload-picture', [ProfilePictureController::class, 'upload']);
        Route::get('/show-profile-picture', [ProfilePictureController::class, 'show']);
    });
});
