<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionApplication extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'admission_applications';

    protected $fillable = [
        'school_id',
        'campus_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'class_id',
        'status',
        'interview_notes',
        'review_notes',
        'documents',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'documents' => 'array',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }
}
