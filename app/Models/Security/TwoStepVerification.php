<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Security\TwoStepVerification
 *
 * @property int $id
 * @property int $user_id
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwoStepVerification whereUserId($value)
 * @mixin \Eloquent
 */
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
