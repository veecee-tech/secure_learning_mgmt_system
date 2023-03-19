<?php

namespace App\Models\Teacher;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Teacher\Teacher
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $other_name
 * @property string|null $email
 * @property string $phone_number
 * @property string $date_of_birth
 * @property string $enrollment_status
 * @property int $class_level_id
 * @property string|null $parent_first_name
 * @property string|null $parent_last_name
 * @property string|null $parent_phone_number_1
 * @property string|null $parent_phone_number_2
 * @property string|null $parent_home_address
 * @property string|null $parent_emergency_contact
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ClassLevel|null $classLevel
 * @property-read mixed $full_name
 * @property-read mixed $full_name_with_other_name
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereClassLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereEnrollmentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereOtherName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentHomeAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentPhoneNumber1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereParentPhoneNumber2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUserId($value)
 * @mixin \Eloquent
 */
class Teacher extends Model
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
        'class',
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
