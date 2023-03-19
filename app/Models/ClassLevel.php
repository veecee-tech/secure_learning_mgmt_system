<?php

namespace App\Models;

use App\Models\Topic;
use App\Models\Subject;
use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\ClassLevel
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read int|null $students_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subject> $subjects
 * @property-read int|null $subjects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Teacher> $teachers
 * @property-read int|null $teachers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Topic> $topics
 * @property-read int|null $topics_count
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClassLevel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];


    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function topics(): HasManyThrough
    {
        return $this->hasManyThrough(Topic::class, Subject::class);
    }


}
