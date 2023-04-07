<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Models\Student\Student;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Models\Teacher\Teacher;

class DashboardController extends BaseController
{
    //

//create get total students annotation
/**
 * @OA\Get(
 *     path="/api/admin/dashboard/total-students",
 *    summary="Get total students",
 *    tags={"Admin Dashboard"},
 * security={{"Bearer":{}}},
 *   description="Get total students",
 *  operationId="getTotalStudents",
 * 
 *    @OA\Response(
 *        response=200,
 *       description="Success",
 *     @OA\JsonContent(
 *         @OA\Property(
 *            property="success",
 *          type="boolean",
 *        example=true
 *       ),
 *     @OA\Property(
 *       property="data",
 *    type="object",
 * @OA\Property(
 *   property="total_students",
 * type="integer",
 * example=10
 * )
 * )
 * )
 * ),
 * @OA\Response(
 *   response=401,
 * description="Unauthenticated",
 * @OA\JsonContent(
 *  @OA\Property(
 *  property="message",
 * type="string",
 * example="Unauthenticated."
 * )
 * )
 * ),
 * @OA\Response(
 *  response=403,
 * description="Forbidden",
 * @OA\JsonContent(
 * @OA\Property(
 * property="message",
 * type="string",
 * example="Forbidden"
 * )
 * )
 * )
 * )
 * 
 */ 
    public function getTotalStudents()
    {
        $totalStudents = Student::count();

        $success['total_students'] = $totalStudents;

        return $this->sendResponse($success, 'Total Students', 200);
    }

 //create get total teachers annotation
/**
 * @OA\Get(
 *    path="/api/admin/dashboard/total-teachers",
 *  summary="Get total teachers",
 * tags={"Admin Dashboard"},
 * description="Get total teachers",
 * operationId="getTotalTeachers",
 * security={{"Bearer":{}}},
 * 
 * @OA\Response(
 *  response=200,
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
 * @OA\Property(
 * property="total_teachers",
 * type="integer",
 * example=10
 * )
 * )
 * )
 * ),
 * @OA\Response(
 * response=401,
 *  description="Unauthenticated",
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
 * example="Forbidden"
 * )
 * )
 * )
 * )
 * 
 */    
    public function getTotalTeachers()
    {
        $totalTeachers = Teacher::count();

        $success['total_teachers'] = $totalTeachers;

        return $this->sendResponse($success, 'Total Teachers', 200);
    }

}
