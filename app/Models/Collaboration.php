<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Collaboration extends Model
{
    protected $table = 'collaboration';
    protected $primaryKey = 'collaboration_id';
    protected $fillable = [
        'categories_id',
        'user'
    ];

    public static function getCollaboratedCategories($user){
        return DB::table('collaboration')
                   ->join('categories', 'collaboration.categories_id', '=', 'categories.categories_id')
                   ->where('collaboration.user', $user)
                   ->select('categories.*')
                   ->get();
    }

    public static function getCollaboratedCategoryUsers($categoryId){
        return DB::table('collaboration')
                   ->join('users', 'collaboration.user', '=', 'users.email')
                   ->select(['users.name', 'users.email'])
                   ->where('collaboration.categories_id', $categoryId)
                   ->get();
    }
}
