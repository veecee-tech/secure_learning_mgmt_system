<?php

namespace App\Models\Student;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'other_name',
        'email',
        'phone_number',
        'date_of_birth',
        'enrollment_status',
        'class_level_id',
        'parent_first_name',
        'parent_last_name',
        'parent_phone_number_1',
        'parent_phone_number_2',
        'parent_home_address',
        'parent_emergency_contact',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameWithOtherNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ' ' . $this->other_name;
    }

    public function classLevel(): BelongsTo
    {
        return $this->belongsTo(ClassLevel::class);
    }

}
