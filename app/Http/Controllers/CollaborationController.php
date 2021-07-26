<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Collaboration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollaborationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'sucess',
            'categories' => Collaboration::getCollaboratedCategories($request->user()->email)
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
        if($isCategoryExist == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized Category'
            ], 403);
        }

        $validation = $this->validation($request);
        if($validation->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validation->errors()->all()
            ], 401);
        }

        $isUserCollaborated = $this->checkUserPermitted($categoryId, $request->email);
        if($isUserCollaborated != null){
            return response()->json([
                'status' => 'error',
                'message' => 'User is already collaborated'
            ], 400);
        }

        Collaboration::create([
            'categories_id' => $categoryId,
            'user' => $request->user
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User has been added to collaboration'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($categoryId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if($isCategoryExist == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
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

        return response()->json([
            'status' => 'success',
            'users' => Collaboration::getCollaboratedCategoryUsers($categoryId)
        ], 200);
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
    public function destroy($categoryId, Request $request)
    {
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if($isCategoryExist == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized Category'
            ], 403);
        }

        $validation = $this->validation($request);
        if($validation->fails()){
            return response([
                'status' => 'error',
                'message' => $validation->errors()->all()
            ], 403);
        }

        $isUserCollaborated = $this->checkUserPermitted($categoryId, $request->user);
        if($isUserCollaborated == null){
            return response()->json([
                'status' => 'error',
                'message' => 'User is not collaborated'
            ], 400);
        }

        $user = Collaboration::where('user', $request->email);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User has been removed'
        ], 200);
    }

    public function deleteAllUsersFromCategory($categoryId, Request $request){
        $isCategoryExist = $this->checkCategoryExist($categoryId);
        if($isCategoryExist == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $isCategoryOwned = $this->checkCategoryOwner($categoryId, $request->user()->email);
        if($isCategoryOwned == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized Category'
            ], 403);
        }

        Collaboration::where('categories_id', $categoryId)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'All users have been deleted from collab category'
        ], 200);
    }

    private function checkCategoryExist($categoryId){
        return Categories::find($categoryId);
    }

    private function checkCategoryOwner($categoryId, $email){
        return Categories::where('categories_id', $categoryId)->where('created_by', $email)->first();
    }

    private function checkUserPermitted($categoryId, $user){
        return Collaboration::where('categories_id', $categoryId)->where('user', $user)->first();
    }

    private function validation(Request $request){
        $rules = [
            'user' => 'required|email'
        ];
        $messages = [
            'required' => 'The :attribute value is required',
            'email'  => 'The :attribute value must be a valid email'
        ];
        return Validator::make($request->all(),$rules, $messages);
    }
}
