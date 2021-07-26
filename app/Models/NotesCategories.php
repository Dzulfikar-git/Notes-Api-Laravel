<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotesCategories extends Model
{
    protected $table = 'notes_in_categories';
    protected $primaryKey = 'notes_categories_id';
    protected $fillable = [
        'categories_id',
        'notes_id'
    ];

    public static function getAllNotesInCategory($categoryId){
        return DB::table('notes_in_categories')
                    ->join('notes', 'notes.notes_id', '=', 'notes_in_categories.notes_id')
                    ->where('notes_in_categories.categories_id', $categoryId)
                    ->select('notes.*')
                    ->get();
    }

}
