<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'notes_id';
    protected $fillable = [
        'title',
        'content',
        'created_by',
    ];

}
