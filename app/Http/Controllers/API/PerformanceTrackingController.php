<?php

namespace App\Http\Controllers\API;

use App\Models\Subject;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Models\Student\Student;
use App\Models\PerformanceTracking;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

// //create swagger documentation for this method

    /**
     * @OA\Post(
     *      path="/api/performance/store-performance-tracking/{subject_id}/{student_id}",
     *      operationId="storePerformanceTracking",
     *      tags={"Performance Tracking"},
     *      summary="Store a newly created performance tracking record in storage.",
     *      description="Returns the performance tracking record data",
     * security={{"Bearer":{}}},
     *  @OA\Parameter(
     * name="student_id",
     * in="path",
     * description="Student id",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example="1"
     * )
     * ),
     * @OA\Parameter(
     * name="subject_id",
     * in="path",
     * description="Subject id",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example="1"
     * )
     * ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"time_spent"},
     *             @OA\Property(
     *                 property="time_spent",
     *                type="integer",
     *               example="10"
     *            ),
     *       ),
     *     ),
     *     @OA\Response(
     *        response=200,
     *       description="successful operation",
     *     @OA\JsonContent(
     *      @OA\Property(
     *         property="success",
     *       type="boolean"
     *   ),
     * @OA\Property(
     * property="data",
     * type="object",
     * example={
     * "id": 1,
     * "student_id": 1,
     * "subject_id": 1,
     * "time_spent": 10,
     *  "created_at": "2021-03-23T15:12:12.000000Z",
     * "updated_at": "2021-03-23T15:12:12.000000Z"
     * }
     * ),
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Performance tracking record created successfully"
     * )
     * )
     * )
     * )
     * )
     * 
     */ 
class PerformanceTrackingController extends BaseController
{
    

    public function store(Request $request, $subject_id, $student_id){

        $validator = Validator::make($request->all(), [
            'time_spent' => 'required|integer|min:0'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $student = Student::findOrFail($student_id);
        $subject = Subject::findOrFail($subject_id);

        $time_spent = $request->time_spent;

        $record = $subject->performanceTrackings()->where('subject_id', $subject->id)->first();

        // dd($record);
        if($record && $record->created_at->isToday()){
            $record->time_spent += $time_spent;
            $record->update([
                'time_spent' => $record->time_spent,
                'student_id' => $student->id,
            ]);
        }else{
            $student->performanceTrackings()->create([
                'subject_id' => $subject->id,
                'time_spent' => $time_spent,
                'student_id' => $student->id,
            ]);
        }

        return $this->sendResponse($student->performanceTrackings, 'Performance Tracking created successfully.', 201);

    }

    //create annotation for this method
    /**
     * @OA\Get(
     *     path="/api/performance/get-all-performance-tracking-records-by-days-and-subject-id/{subject_id}/{number_of_days}",
     *    operationId="getAllPerformanceTrackingRecordsByDaysAndSubjectID",
     *    tags={"Performance Tracking"},
     *   summary="Get all performance tracking records by days and subject id",
     *  description="Returns the performance tracking records data",
     * security={{"Bearer":{}}},
     * 
     * @OA\Parameter(
     * name="subject_id",
     * in="path",
     * description="Subject id",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example="1"
     * )
     * ),
     * @OA\Parameter(
     * name="number_of_days",
     * in="path",
     * description="Number of days",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example="7"
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
     * property="data",
     * type="array",
     * @OA\Items(
     * example={
     * "day_of_the_week": "Monday",
     * "day_of_the_month": "22",
     * "time_spent": 10
     * }
     * )
     * ),
     * @OA\Property(
     * property="message",
     * type="string",
     * example="Performance tracking records retrieved successfully"
     * )
     * )
     * )
     * )
     * 
     */
    public function getAllPerformanceTrackingRecordsByDaysAndSubjectID($subject_id, $number_of_days){

        $records = PerformanceTracking::with('subject')
        ->whereHas('subject', function($query) use ($subject_id){
            $query->where('subject_id', $subject_id);
        })->where('created_at', '>=', now()->subDays($number_of_days))->get();

        //get the time spent for each day of the week
        $data = [];
        
        $total_time_spent = 0;
        foreach($records as $record){
            $total_time_spent += $record->sum('time_spent');
            $data[] = [
                'day_of_the_week' => $record->created_at->format('l'),
                'day_of_the_month' => $record->created_at->format('d'),

                'time_spent' => array_key_exists('day_of_the_week', $data) && array_key_exists('day_of_the_month', $data) ?
                        $record->sum('time_spent') : $record->time_spent,

                //check if a value exist in an array

            ];
        }
        return $this->sendResponse($data, 'Performance Tracking records retrieved successfully.', 200);
    }
}