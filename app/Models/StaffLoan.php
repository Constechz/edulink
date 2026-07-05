<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToSchool;

class StaffLoan extends Model
{
    use BelongsToSchool;

    protected $table = 'staff_loans';

    protected $fillable = ['school_id', 'staff_id', 'amount', 'repayment_term_months', 'monthly_installment', 'balance', 'status'];
}
