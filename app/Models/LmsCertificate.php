<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;

class LmsCertificate extends Model
{
    use BelongsToSchool;

    protected $table = 'lms_certificates';

    protected $fillable = [
        'school_id',
        'course_id',
        'student_id',
        'certificate_number',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(LmsCourse::class, 'course_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
