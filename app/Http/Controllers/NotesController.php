<?php

namespace App\Http\Controllers;

use App\Models\Notes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Notes::where('created_by', Auth::user()->email)->get();
        return response()->json([
            'status' => 'success',
            'notes' => $notes
        ], 200);
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
            'title' => ['required'],
            'content' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        Notes::create([
            'title' => $request->title,
            'content' => $request->content,
            'created_by' => $request->user()->email,
        ]);
        return response()->json([
            "status" => "success",
            "message" => "Note Has Been Created"
        ], 201); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $note =  Notes::find($id);
        if(!$note){
            return response()->json([
                'status' => 'error',
                'message' => 'Not Found'
            ], 404);
        }
        if($note->created_by == Auth::user()->email){
            return response()->json([
                'status' => 'success',
                'note' => $note
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
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
        $note = Notes::find($id);
        if(!$note){
            return response()->json([
                'status' => 'error',
                'message' => 'Note Not Found'
            ], 404);
        }

        if($note->created_by != Auth::user()->email){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'content' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }

        if($validator){
            $note->title = $request->title;
            $note->content = $request->content;
            $note->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Note Has Been Updated'
            ], 201);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $note = Notes::find($id);
        if(!$note){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }
        if($note->created_by != Auth::user()->email){
            return response()->json([
                'status' => 'success',
                'note' => $note
            ], 200);
        }
        $note->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Note Has Been Deleted'
        ], 200);
    }
}
