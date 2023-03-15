<?php

namespace App\Models;

use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Security\TwoStepVerification;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;




class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['username', 'phone_number', 'password'];

    protected $hidden = ['email', 'name'];
    

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function twoStepVerification(): HasOne
    {
        return $this->hasOne(TwoStepVerification::class);
    }
    
}
