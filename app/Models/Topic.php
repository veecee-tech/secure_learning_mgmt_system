<?php

namespace App\Models;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_id',
        'content',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    
}
