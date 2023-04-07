<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Topic;
use App\Models\Subject;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;

class CurriculumController extends BaseController
{
    //

//create annotation for store subject
/**
 * @OA\Post(
 *    path="/api/admin/subject/store",
 *   summary="Store subject",
 *  tags={"Curriculum"},
 * description="Store subject",
 * operationId="storeSubject",
 * security={{"Bearer":{}}},
 * @OA\RequestBody(
 *   required=true,
 * @OA\JsonContent(
 * @OA\Property(
 * property="subject_name",
 * type="string",
 * example="Mathematics"
 * ),
 * @OA\Property(
 * property="class",
 * type="string",
 * example="JSS1"
 * ),
 * @OA\Property(
 * property="topic_files",
 * type="array",
 * @OA\Items(
 * type="file"
 * )
 * )
 * )
 * ),
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
 * @OA\Property(
 * property="subject",
 * type="object",
 * @OA\Property(
 * property="id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * example="Mathematics"
 * ),
 * @OA\Property(
 * property="class_level_id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="created_at",
 * type="string",
 * example="2020-10-10 10:10:10"
 * ),
 * @OA\Property(
 * property="updated_at",
 * type="string",
 *  
 * example="2020-10-10 10:10:10"
 * )
 * )
 * )
 * )
 * ),
 * @OA\Response(
 * response=401,
 * description="Unauthenticated",
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
 * example="Forbidden."
 * )
 * )
 * ),
 * @OA\Response(
 * response=422,
 * description="Validation Error",
 * @OA\JsonContent(
 * @OA\Property(
 * property="message",
 * type="string",
 * example="Validation Error."
 * ),
 * @OA\Property(
 * property="errors",
 * type="object",
 * @OA\Property(
 * property="subject_name",
 * type="array",
 * @OA\Items(
 * type="string",
 * example="The subject name field is required."
 * )
 * ),
 * @OA\Property(
 * property="class",
 * type="array",
 * @OA\Items(
 * type="string",
 * example="The selected class is invalid."
 * )
 * ),
 *  
 * @OA\Property(
 * property="topic_files",
 * type="array",
 * @OA\Items(
 * type="string",
 * example="The topic files field is required."
 * )
 * ),
 * @OA\Property(
 * property="topic_files.*",
 * type="array",
 * @OA\Items(
 * type="string",
 * example="The topic files.* field is required."
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 *  
 */ 

//create annotation for create single topic
/**
 * @OA\Post(
 *   path="/api/admin/topic/create-single-topic",
 *  summary="Store topic",
 * tags={"Curriculum"},
 * description="Store topic",
 * operationId="storeTopic",
 * 
 * security={{"Bearer":{}}},
 * @OA\RequestBody(
 *  required=true,
 * @OA\JsonContent(
 * @OA\Property(
 * property="subject_id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="topic_file",
 * type="file",
 * example="topic.pdf"
 * )
 * )
 * ),
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
 * @OA\Property(
 * property="topic",
 * type="object",
 * @OA\Property(
 * property="id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="subject_id",
 * type="integer",
 * example=1
 * ),
 * @OA\Property(
 * property="file_path",
 * type="string",
 * example="public/topic.pdf"
 * ),
 * @OA\Property(
 * property="file_name",
 * type="string",
 * example="topic.pdf"
 * ),
 * @OA\Property(
 *  
 * property="created_at",
 * type="string",
 * example="2020-10-10 10:10:10"
 * ),
 * @OA\Property(
 * property="updated_at",
 * type="string",
 * example="2020-10-10 10:10:10"
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 * 
 */
    public function store(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string',
            'class' => 'required|exists:class_levels,name',
            'topic_files' => 'required|array',
            'topic_files.*' => 'required|file'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        $subject = new Subject();
        $subject->name = $request->input('subject_name');
        $subject->class_level_id = ClassLevel::where('name', $request->class)->first()->id;
        $subject->save();
        
        foreach ($request->file('topic_files') as $file) {
            
            $file_path = $file->store('public');
           
            $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            //get the file encoding
            $file_encoding = mb_detect_encoding(Storage::get($file_path), 'UTF-8, ISO-8859-1', true);
            
            //check if it is english supported encoding
            if ($file_encoding != 'UTF-8') {
                //convert to utf-8
                $file_contents = mb_convert_encoding(Storage::get($file_path), 'UTF-8', $file_encoding);
            } else {
                $file_contents = Storage::get($file_path);
            }

            // $file_contents = Storage::get($file_path);
            $topic = new Topic();
            $topic->name = $file_name;
            $topic->content = $file_contents;
            $topic->subject_id = $subject->id;
            $topic->save();
        }

        return $this->sendResponse($subject, 'Subject created successfully.', 201);
    }

    public function createSingleTopic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'topic_file' => 'required|file'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $file_path = $request->file('topic_file')->store('public');
        $file_name = pathinfo($request->file('topic_file')->getClientOriginalName(), PATHINFO_FILENAME);

        //get the file encoding
        $file_encoding = mb_detect_encoding(Storage::get($file_path), 'UTF-8, ISO-8859-1', true);
        
        //check if it is english supported encoding
        if ($file_encoding != 'UTF-8') {
            //convert to utf-8
            $file_contents = mb_convert_encoding(Storage::get($file_path), 'UTF-8', $file_encoding);
        } else {
            $file_contents = Storage::get($file_path);
        }

        $topic = new Topic();
        $topic->name = $file_name;
        $topic->content = $file_contents;
        $topic->subject_id = $request->subject_id;
        $topic->save();

        return $this->sendResponse($topic, 'Topic created successfully.', 201);
    }
/**
 * @OA\Get(
 *     path="/api/admin/class/all",
 *     summary="Get all class levels, subjects and topics",
 *     tags={"Curriculum"},
 *      security={{"Bearer":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="class_levels",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example="1"),
 *                     @OA\Property(property="name", type="string", example="JSS 1"),
 *                     @OA\Property(property="subjects", type="array", @OA\Items(
 *                         @OA\Property(property="id", type="integer", example="1"),
 *                         @OA\Property(property="name", type="string", example="Mathematics"),
 *                         @OA\Property(property="topics", type="array", @OA\Items(
 *                             @OA\Property(property="id", type="integer", example="1"),
 *                             @OA\Property(property="name", type="string", example="Introduction to Algebra"),
 *                         ))
 *                     ))
 *                 )
 *             )
 *         )
 *     )
 * )
 */

    public function allClassLevels()
    {
        $class_levels = ClassLevel::with(
            'subjects.topics'
        )->get();
        return $this->sendResponse($class_levels, 'Class levels retrieved successfully.', 200);
    }


//create annotation get single class level

/**
 * @OA\Get(
 *    path="/api/admin/class/{id}",
 *   summary="Get single class level, subjects and topics",
 *  tags={"Curriculum"},
 * security={{"Bearer":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * description="Class level id",
 * required=true,
 * @OA\Schema(
 * type="integer",
 * example="1"
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Success",
 * @OA\JsonContent(
 * @OA\Property(
 * property="id",
 * type="integer",
 * example="1"
 * ),
 * @OA\Property(
 * property="name",
 *  
 * type="string",
 * example="JSS 1"
 * ),
 * @OA\Property(
 * property="subjects",
 * type="array",
 * @OA\Items(
 * @OA\Property(
 * property="id",
 * type="integer",
 * example="1"
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * example="Mathematics"
 * ),
 * @OA\Property(
 * property="topics",
 * type="array",
 * @OA\Items(
 * @OA\Property(
 * property="id",
 * type="integer",
 * example="1"
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * example="Introduction to Algebra"
 * )
 * )
 * 
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 * )
 * 
 */ 
    public function getSingleClass($id)
    {
        $class_level = ClassLevel::with(
            'subjects.topics'
        )->where('id', $id)->first();

        return $this->sendResponse($class_level, 'Class level retrieved successfully.', 200);
    }

 //create annotation for get all subjects
 
    /**
     * @OA\Get(
     *    path="/api/admin/subjects/all",
     *  summary="Get all subjects, and topics",
     * tags={"Curriculum"},
     * security={{"Bearer":{}}},
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="subjects",
     * type="array",
     * @OA\Items(
     * @OA\Property(
     * property="id",
     * type="integer",
     * example="1"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Mathematics"
     * ),
     * @OA\Property(
     * property="topics",
     * type="array",
     * @OA\Items(
     * @OA\Property(
     * property="id",
     * type="integer",
     * example="1"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Introduction to Algebra"
     * 
     * )
     * )
     * )
     * )
     * )
     * )
     * )
     * )
     * 
     */
    public function allSubjects()
    {
        $subjects = Subject::with('topics')->get();
        return $this->sendResponse($subjects, 'Subjects retrieved successfully.', 200);
    }


    //create annotation for get subjects by class level

    /*
    *
    * @OA\Get(
    * path="/api/admin/class/{id}/subjects",
    * summary="Get all subjects and topics by class level",
    * tags={"Curriculum"},
    * security={{"Bearer":{}}},
    * @OA\Parameter(
    * name="id",
    * in="path",
    * description="Class level id",
    * required=true,
    * @OA\Schema(
    * type="integer",
    * example="1"
    * )
    * ),
    * @OA\Response(
    * response=200,
    * description="Success",
    * @OA\JsonContent(
    * @OA\Property(
    * property="subjects",
    * type="array",
    * @OA\Items(
    * @OA\Property(
    * property="id",
    * type="integer",
    * example="1"
    * ),
    * @OA\Property(
    * property="name",
    * type="string",
    * example="Mathematics"
    * ),
    * @OA\Property(
    * property="topics",
    * type="array",
    * @OA\Items(
    * @OA\Property(
    * property="id",
    * type="integer",
    * example="1"
    * ),
    * @OA\Property(
    * property="name",
    * type="string",
    * example="Introduction to Algebra"
    * )
    * )
    * )
    * )
    * )
    * )
    * )
    * )
    * )
    * 
    */

    public function getSubjectsByClass($id)
    {
        $subjects = Subject::with('topics')->where('class_level_id', $id)->get();
        return $this->sendResponse($subjects, 'Subjects retrieved successfully.', 200);
    }

    //create annotation for get single subject

    /**
     * @OA\Get(
     * path="/api/admin/class/{id}/subject/{subject_id}",
     * summary="Get single subject and topics by class level",
     * tags={"Curriculum"},
     * security={{"Bearer":{}}},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * description="Class level id",
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
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="id",
     * type="integer",
     * example="1"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Mathematics"
     * ),
     * @OA\Property(
     * property="topics",
     * type="array",
     * @OA\Items(
     * @OA\Property(
     * property="id",
     * type="integer",
     * example="1"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Introduction to Algebra"
     * )
     * )
     * )
     * )
     * )
     * )
     * 
     */
    public function getSingleSubject($id, $subject_id)
    {
        $subject = Subject::with('topics')->where(
            [
                ['id', $subject_id],
                ['class_level_id', $id]
            ]
        )->first();
        
        return $this->sendResponse($subject, 'Subject retrieved successfully.', 200);
    }

    //create annotation for get single topic

    /**
     * 
     * @OA\Get(
     * path="/api/student/read/topic/{topic_id}",
     * summary="Get single topic by class level",
     * tags={"Students"},
     * 
     * security = {{"Bearer":{}}},
     * 
     * @OA\Parameter(
     * name="topic_id",
     * in="path",
     * description="Topic id",
     * required=true,
     * @OA\Schema(
     * type="integer",
     * example="1"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Success",
     * @OA\JsonContent(
     * @OA\Property(
     * property="id",
     * type="integer",
     * example="1"
     * ),
     * @OA\Property(
     * property="name",
     * type="string",
     * example="Introduction to Algebra"
     * )
     * )
     * )
     * )
     *  
     * 
     */

     public function readTopic($topic_id)
     {
         $topic = Topic::find($topic_id);
         return $this->sendResponse($topic, 'Topic retrieved successfully.', 200);
     }

     //create annotation for get topics by subject id
     
        /**
         * 
         * @OA\Get(
         * path="/api/student/read/subject/{subject_id}/topics",
         * summary="Get all topics by subject id",
         * tags={"Students"},
         * 
         * security = {{"Bearer":{}}},
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
         * @OA\Response(
         * response=200,
         * description="Success",
         * @OA\JsonContent(
         * @OA\Property(
         * property="topics",
         * type="array",
         * @OA\Items(
         * @OA\Property(
         * property="id",
         * type="integer",
         * example="1"
         * ),
         * @OA\Property(
         * property="name",
         * type="string",
         * example="Introduction to Algebra"
         * ),
         * @OA\Property(
         * property="content",
         * type="string",
         * example="Introduction to Algebra"
         * )
         * )
         * )
         * )
         * )
         * )
         * 
         */

         public function getAllTopicsBySubject($subject_id)
         {
             $topics = Topic::where('subject_id', $subject_id)->get();
             return $this->sendResponse($topics, 'Topics retrieved successfully.', 200);
         }
}



