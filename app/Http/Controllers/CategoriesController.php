<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::where('created_by', Auth::user()->email)->get();
        return response()->json([
            'status' => 'success',
            'categories' => $categories
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
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }

        Categories::create([
            "title" => $request->title,
            "created_by" => $request->user()->email
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category Has Been Added'
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categories = Categories::find($id);
        if(!$categories){
            return response()->json([
                'status' => 'error',
                'message' => 'Category not Found'
            ], 404);
        }

        if($categories->created_by != Auth::user()->email){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'categories' => $categories
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
        $category = Categories::find($id);
        if(!$category){
            return response()->json([
                'status' => 'error',
                'message' => 'Not Found'
            ], 404);
        }

        if($category->created_by != Auth::user()->email){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required'],
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }

        if($validator){
            $category->title = $request->title;
            $category->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Category Has Been Updated'
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
        $category = Categories::find($id);
        if(!$category){
            return response()->json([
                'status' => 'error',
                'message' => 'Not Found'
            ], 404);
        }

        if($category->created_by != Auth::user()->email){
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Category Has Been Deleted'
        ], 200);
    }
}
