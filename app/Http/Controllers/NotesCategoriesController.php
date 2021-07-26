<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Collaboration;
use App\Models\Notes;
use App\Models\NotesCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotesCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($categoryId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if(!$isCategoryExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Category is not found'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            $isUserPermitted = $this->checkUserPermitted($categoryId, $request->user()->email);
            if($isUserPermitted == null){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not permitted to access this category'
                ], 403);
            }
        }

        $notesInCategory = Categories::find($categoryId);
        if(!$notesInCategory){
            return response()->json([
                'status' => 'error',
                'message' => 'Not Found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'category_title' => Categories::find($categoryId)->title,
            'notes' => NotesCategories::getAllNotesInCategory($categoryId)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($categoryId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if(!$isCategoryExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Category is not found'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            $isUserPermitted = $this->checkUserPermitted($categoryId, $request->user()->email);
            if($isUserPermitted == null){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not permitted to access this category'
                ], 403);
            }
        }

        $valid = $this->requestValidation($request);
        if($valid->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $valid->errors()->all()
            ], 400);
        }
        
        $isNoteExist = $this->checkNoteExist($request->note_id);
        if(!$isNoteExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Note Id Not Found'
            ], 404);
        }

        $isNoteOwned = $this->checkNoteOwner($request->note_id, $request->user()->email);
        if($isNoteOwned == null){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not permitted to add this note'
            ], 403);
        }

        $isNoteAlreadyAdded = $this->checkNoteAlreadyAdded($request->note_id, $categoryId);
        if($isNoteAlreadyAdded != null){
            return response()->json([
                'status' => 'error',
                'message' => 'Note exists in category'
            ], 400);
        }

        NotesCategories::create([
            'notes_id' => $request->note_id,
            'categories_id' => $categoryId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Note has been added to category'
        ], 201);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($categoryId, $noteId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if(!$isCategoryExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Category is not exist'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            $isUserPermitted = $this->checkUserPermitted($categoryId, $request->user()->email);
            if($isUserPermitted == null){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not permitted to access this category'
                ], 403);
            }
        }

        $isNoteExist = $this->checkNoteExist($noteId);
        if(!$isNoteExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Note is note exist'
            ], 404);
        }

        $note = NotesCategories::all()->where('notes_id', $noteId)->where('categories_id', $categoryId);
        return response()->json([
            'status' => 'success',
            'note' => $note
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($categoryId, $noteId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if(!$isCategoryExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Category is not exist'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            $isUserPermitted = $this->checkUserPermitted($categoryId, $request->user()->email);
            if($isUserPermitted == null){
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not permitted to access this category'
                ], 403);
            }
        }

        $isNoteExist = $this->checkNoteExist($noteId);
        if(!$isNoteExist){
            return response()->json([
                'status' => 'error',
                'message' => 'Note is note exist'
            ], 404);
        }

        NotesCategories::where('categories_id', $categoryId)->where('notes_id', $noteId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Note has been removed from category'
        ], 200);
    }

    private function requestValidation(Request $request){
        $rules = [
            'note_id' => 'required|alpha_num'
        ];
        $messages = [
            'required' => 'The :attribute value is required',
            'alpha_num' => 'The :attribute value must be alpha numeric'
        ];
        return Validator::make($request->all(),$rules, $messages);
    }

    private function checkCategoryExist($categoryId){
        return Categories::find($categoryId);
    }

    private function checkCategoryOwner($categoryId, $user){
        return Categories::where('categories_id', $categoryId)->where('created_by', $user)->first();
    }

    private function checkUserPermitted($categoryId, $user){
        return Collaboration::where('categories_id', $categoryId)->where('user', $user)->first();
    }

    private function checkNoteExist($noteId){
        return Notes::find($noteId);
    }

    private function checkNoteOwner($noteId, $user){
        return Notes::where('notes_id', $noteId)->where('created_by', $user)->first();
    }

    private function checkNoteAlreadyAdded($noteId, $categoryId){
        return NotesCategories::where('notes_id', $noteId)->where('categories_id', $categoryId)->first();
    }
}
