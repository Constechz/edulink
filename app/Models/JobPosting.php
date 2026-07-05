<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class JobPosting extends Model
{
    use BelongsToSchool;

    protected $table = 'job_postings';

    protected $fillable = ['school_id', 'posted_by_alumni_id', 'job_title', 'company_name', 'location', 'job_description', 'application_url', 'closing_date'];
}
