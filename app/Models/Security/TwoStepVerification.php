<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoStepVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
