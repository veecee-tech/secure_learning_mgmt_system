<?php

namespace App\Http\Controllers\API\Teacher;

use Illuminate\Http\Request;
use App\Models\Teacher\Assignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use App\Models\Teacher\Teacher;

class AssigmentController extends BaseController
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'subject_id' => 'required|numeric|exists:subjects,id',
            'class_level_id' => 'required|numeric|exists:class_levels,id',
            'visibility' => 'nullable|in:show,hidden',
            'assignment_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $file_path = $request->file('assignment_file')->store('public/assignment_files');
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

        //get auth user
        $user = auth()->user();
        $auth_teacher = Teacher::where('user_id', $user->id)->first();

        if(!$auth_teacher) {
            return $this->sendError('Teacher not found. Only teachers can create assignments.');
        }
        $assignment = new Assignment();
        $assignment->subject_id = $request->subject_id;
        $assignment->teacher_id = $auth_teacher->id;
        $assignment->class_level_id = $request->class_level_id;
        $assignment->visibility = $request->visibility;
        $assignment->content = $file_contents;
        $assignment->save();
        $assignment->refresh();

        return $this->sendResponse($assignment, 'Assignment created successfully.');

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
}
