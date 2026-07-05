<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use BelongsToSchool, SoftDeletes;

    protected $fillable = [
        'school_id',
        'campus_id',
        'student_id_number',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'nationality',
        'religion',
        'blood_group',
        'photo',
        'address',
        'region',
        'district',
        'has_disability',
        'disability_notes',
        'house_id',
        'scholarship_id',
        'previous_school',
        'transfer_date',
        'transfer_reason',
        'current_class_id',
        'current_stream_id',
        'enrollment_date',
        'status',
        'nhis_number',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'transfer_date' => 'date',
        'enrollment_date' => 'date',
        'has_disability' => 'boolean',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function currentClass()
    {
        return $this->belongsTo(SchoolClass::class, 'current_class_id');
    }

    public function currentStream()
    {
        return $this->belongsTo(Stream::class, 'current_stream_id');
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'student_guardians');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function scores()
    {
        return $this->hasMany(StudentScore::class);
    }
}
