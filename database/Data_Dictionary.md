# EduSphere Ghana ERP - Database Data Dictionary
Version 2.1 | June 2026

This document provides a comprehensive map of all database tables, columns, types, indexes, and keys configured for the EduSphere school management system.

---

## 1. Platform Infrastructure & Tenant System

### `plans`
- **Purpose**: Defines subscription plan tiers and resource limit allocations.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `name` (varchar, unique: Free, Standard, Premium, Enterprise)
  - `price_monthly` (decimal 8,2)
  - `price_yearly` (decimal 8,2)
  - `max_students` (int, -1 = unlimited)
  - `max_staff` (int, -1 = unlimited)
  - `max_campuses` (int, -1 = unlimited)
  - `features` (json: enabled modules array)
  - `sms_credits_monthly` (int)
  - `storage_gb` (int)
  - `is_active` (boolean)
  - `created_at`, `updated_at` (timestamps)

### `schools`
- **Purpose**: Master table for all school tenants (multi-tenancy root).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `name`, `short_name` (varchars)
  - `school_code` (varchar, unique, e.g., 'GVIS')
  - `logo` (varchar, nullable)
  - `address`, `region`, `district` (varchars, nullable)
  - `phone`, `email` (varchars, nullable)
  - `website_domain`, `custom_domain`, `subdomain` (varchars, unique)
  - `plan_id` (FK to `plans`)
  - `subscription_status` (varchar: trial, active, suspended, expired)
  - `trial_ends_at` (timestamp, nullable)
  - `owner_name`, `owner_email`, `owner_phone` (varchars)
  - `branding` (json: colors, typography settings)
  - `settings` (json: timezone, currency, general parameters)
  - `sms_gateway_config` (text, encrypted credentials)
  - `email_config` (text, encrypted credentials)
  - `is_active`, `onboarding_completed` (booleans)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `campuses`
- **Purpose**: Multi-campus division per school.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `name`, `code` (varchars)
  - `address`, `phone`, `email` (varchars, nullable)
  - `principal_name` (varchar, nullable)
  - `is_main`, `is_active` (booleans)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `subscriptions`
- **Purpose**: Subscription transaction logging.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `plan_id` (FK to `plans`, cascade)
  - `starts_at`, `ends_at` (datetimes)
  - `amount_paid` (decimal 8,2)
  - `currency` (varchar, default 'GHS')
  - `payment_reference`, `payment_method` (varchars, nullable)
  - `status` (varchar: active, expired, canceled)
  - `auto_renew` (boolean)
  - `created_at`, `updated_at` (timestamps)

### `sms_credit_ledger`
- **Purpose**: Immutable ledger logging SMS credit additions and consumption per school.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `type` (enum: purchase, usage, refund)
  - `credits` (int)
  - `balance_after` (int)
  - `reference`, `note` (varchars, nullable)
  - `created_at` (timestamp, nullable)

### `feature_flags`
- **Purpose**: Custom feature toggles per school tenant.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `feature_key` (varchar)
  - `is_enabled` (boolean)
  - `created_at`, `updated_at` (timestamps)
  - **Index**: Unique composite `[school_id, feature_key]`

---

## 2. Access Control (RBAC) & Users

### `roles`
- **Purpose**: System and custom roles per tenant (or global template roles if school_id is null).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, nullable, cascade)
  - `name`, `slug` (varchars)
  - `description` (varchar, nullable)
  - `is_system` (boolean: cannot be deleted)
  - `created_at`, `updated_at` (timestamps)
  - **Index**: Unique composite `[school_id, slug]`

### `permissions`
- **Purpose**: Fine-grained authorization permissions.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `name`, `slug` (varchars, unique)
  - `module` (varchar, e.g., Academics, Finance, LMS)
  - `description` (varchar, nullable)
  - `created_at`, `updated_at` (timestamps)

### `role_permissions`
- **Purpose**: Many-to-many pivot mapping permissions to roles.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `role_id` (FK to `roles`, cascade)
  - `permission_id` (FK to `permissions`, cascade)

### `users`
- **Purpose**: Platform and school admin/staff unified authentication model.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, nullable, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `name`, `email` (varchars)
  - `phone` (varchar, nullable)
  - `password` (varchar, hashed)
  - `role_id` (FK to `roles`, nullable, nullOnDelete)
  - `profile_photo`, `employee_id` (varchars, nullable)
  - `gender` (enum: Male, Female, Other, nullable)
  - `date_of_birth` (date, nullable)
  - `address` (varchar, nullable)
  - `is_active` (boolean)
  - `email_verified_at` (timestamp, nullable)
  - `mfa_secret` (varchar, nullable)
  - `mfa_enabled` (boolean)
  - `last_login_at`, `last_login_ip` (nullable)
  - `created_by`, `updated_by` (bigint unsigned, nullable)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)
  - **Index**: Unique composite `[school_id, email]` (multi-tenant isolation)

---

## 3. Academic Structure

### `academic_years`
- **Purpose**: Calendar years mapping (e.g. 2025/2026).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `name` (varchar)
  - `start_date`, `end_date` (dates)
  - `is_current` (boolean)
  - `created_by` (bigint unsigned, nullable)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `terms`
- **Purpose**: Academic terms per year (e.g. Term 1, Term 2, Term 3).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `name` (varchar)
  - `start_date`, `end_date` (dates)
  - `reopening_date` (date, nullable)
  - `is_current` (boolean)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `departments`
- **Purpose**: Institutional departments.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `name`, `code` (varchars)
  - `hod_user_id` (bigint unsigned, nullable)
  - `description` (text, nullable)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `programmes`
- **Purpose**: Academic programs (Primary, JHS, SHS).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `department_id` (FK to `departments`, nullable, nullOnDelete)
  - `name`, `code` (varchars)
  - `duration_years` (int)
  - `level` (enum: Nursery, KG, Primary, JHS, SHS, TVET, Tertiary)
  - `created_at`, `updated_at` (timestamps)

### `classes`
- **Purpose**: Classrooms mapped to programs and years (e.g. Basic 5).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `programme_id` (FK to `programmes`, nullable, nullOnDelete)
  - `name` (varchar)
  - `level` (enum: Nursery, KG, Primary, JHS, SHS, TVET, Tertiary)
  - `class_teacher_id` (bigint unsigned, nullable)
  - `capacity` (int)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `streams`
- **Purpose**: Stream divisions inside a class (e.g. Basic 5A).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `class_id` (FK to `classes`, cascade)
  - `name` (varchar)
  - `class_teacher_id` (bigint unsigned, nullable)
  - `capacity` (int)
  - `created_at`, `updated_at` (timestamps)

### `subjects`
- **Purpose**: Courses of study.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `department_id` (FK to `departments`, nullable, nullOnDelete)
  - `name`, `code` (varchars)
  - `level` (enum: Nursery, KG, Primary, JHS, SHS, TVET, Tertiary)
  - `is_core`, `is_elective` (booleans)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `class_subjects`
- **Purpose**: Mappings allocating subjects to classes, streams, teachers.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `class_id` (FK to `classes`, cascade)
  - `stream_id` (FK to `streams`, nullable, cascade)
  - `subject_id` (FK to `subjects`, cascade)
  - `teacher_id` (bigint unsigned, nullable)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, nullable, cascade)
  - `periods_per_week` (int)
  - `created_at`, `updated_at` (timestamps)

---

## 4. Student & Guardian Records

### `students`
- **Purpose**: Student master biodata records.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `student_id_number` (varchar)
  - `first_name`, `middle_name`, `last_name` (varchars)
  - `date_of_birth` (date)
  - `gender` (enum: Male, Female, Other)
  - `nationality` (varchar)
  - `religion`, `blood_group`, `photo`, `address`, `region`, `district` (nullable)
  - `has_disability` (boolean)
  - `disability_notes` (text, nullable)
  - `house_id`, `scholarship_id` (bigint unsigned, nullable)
  - `previous_school` (varchar, nullable)
  - `transfer_date` (date, nullable)
  - `transfer_reason` (text, nullable)
  - `current_class_id` (FK to `classes`, nullable, nullOnDelete)
  - `current_stream_id` (FK to `streams`, nullable, nullOnDelete)
  - `enrollment_date` (date)
  - `status` (enum: active, graduated, transferred, withdrawn, deceased)
  - `nhis_number` (varchar, nullable)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)
  - **Index**: Unique composite `[school_id, student_id_number]`

### `guardians`
- **Purpose**: Parent/Guardian contact records.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `first_name`, `last_name`, `relationship`, `phone` (varchars)
  - `alt_phone`, `email`, `occupation`, `address`, `photo` (varchars, nullable)
  - `is_primary`, `can_pickup` (booleans)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `student_guardians`
- **Purpose**: Many-to-many pivot between students and guardians.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `student_id` (FK to `students`, cascade)
  - `guardian_id` (FK to `guardians`, cascade)
  - `is_primary` (boolean)

### `student_enrollments`
- **Purpose**: Historical class enrollment records tracking student academic progression.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, nullable, nullOnDelete)
  - `class_id` (FK to `classes`, cascade)
  - `stream_id` (FK to `streams`, nullable, nullOnDelete)
  - `enrollment_date` (date)
  - `status` (varchar)
  - `promoted_from_class_id` (FK to `classes`, nullable, nullOnDelete)
  - `created_at`, `updated_at` (timestamps)

---

## 5. Grading, Scoring & Report Cards

### `grading_scales`
- **Purpose**: School-defined grade tables (e.g. WAEC, standard GES).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `name` (varchar)
  - `level` (varchar: Primary, JHS, SHS)
  - `is_active`, `is_default` (booleans)
  - `created_at`, `updated_at` (timestamps)

### `grading_scale_items`
- **Purpose**: Individual scale items mapping ranges to grades and grade points.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `grading_scale_id` (FK to `grading_scales`, cascade)
  - `grade` (varchar, e.g., 'A1')
  - `min_score`, `max_score` (decimal 5,2)
  - `grade_point` (decimal 4,2, lower is better in WAEC)
  - `description` (varchar, e.g. Excellent, Fail)
  - `display_order` (int)
  - `created_at`, `updated_at` (timestamps)

### `scoring_configurations`
- **Purpose**: Configuration definitions detailing Class Work (SBA) and Exam weight distributions.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `level` (enum: ALL, Nursery, KG, Primary, JHS, SHS, TVET, Tertiary)
  - `subject_id` (FK to `subjects`, nullable, nullOnDelete)
  - `academic_year_id` (FK to `academic_years`, nullable, nullOnDelete)
  - `name` (varchar)
  - `class_score_max` (decimal 8,2, default 50.00)
  - `class_score_weight` (decimal 8,2, default 50.00)
  - `exam_score_max` (decimal 8,2, default 100.00)
  - `exam_score_weight` (decimal 8,2, default 50.00)
  - `grand_total` (decimal 8,2, default 100.00)
  - `rounding_method` (enum: ROUND, FLOOR, CEIL)
  - `decimal_places` (tinyint)
  - `is_active`, `is_default` (booleans)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `score_components`
- **Purpose**: Defines discrete classwork sub-components (Exercises, Quizzes, Projects).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `scoring_configuration_id` (FK to `scoring_configurations`, cascade)
  - `name` (varchar)
  - `max_marks` (decimal 8,2)
  - `display_order` (int)
  - `is_active`, `is_required` (booleans)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)

### `student_scores`
- **Purpose**: Student subject term results. Stores raw sub-scores in JSON and calculated totals for quick reporting.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `class_id` (FK to `classes`, cascade)
  - `stream_id` (FK to `streams`, nullable, nullOnDelete)
  - `subject_id` (FK to `subjects`, cascade)
  - `term_id` (FK to `terms`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `scoring_configuration_id` (FK to `scoring_configurations`, cascade)
  - `teacher_id` (FK to `users`, cascade)
  - `component_scores` (json: e.g. `{"1": 8, "2": 9}`)
  - `raw_class_total` (decimal 8,2)
  - `scaled_class_score` (decimal 8,2)
  - `raw_exam_score` (decimal 8,2)
  - `scaled_exam_score` (decimal 8,2)
  - `grand_total` (decimal 8,2)
  - `grade` (varchar 5)
  - `grade_point` (decimal 4,2)
  - `subject_position`, `total_students` (integers)
  - `remarks` (text)
  - `is_absent_exam` (boolean)
  - `moderation_note` (text, nullable)
  - `status` (enum: draft, submitted, hod_verified, approved, published)
  - `submitted_at`, `hod_verified_at`, `approved_at`, `published_at` (timestamps)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)
  - **Index**: Unique composite `[school_id, student_id, subject_id, term_id, academic_year_id]`

### `score_history`
- **Purpose**: Immutable audit trail logging all adjustments to student scores.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `student_score_id` (FK to `student_scores`, cascade)
  - `changed_by` (FK to `users`, cascade)
  - `change_type` (varchar: create, update, delete)
  - `old_values`, `new_values` (json, nullable)
  - `reason` (text, nullable)
  - `ip_address` (varchar, nullable)
  - `created_at` (timestamp, nullable)

---

## 6. Promotion Engine

### `promotion_configurations`
- **Purpose**: Level-wise rules determining promotional boundaries at the close of Term 3.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `level` (enum: nursery, kg, primary, jhs, shs, tertiary)
  - `class_id` (FK to `classes`, nullable, nullOnDelete)
  - `method` (enum: annual_average, two_of_three, subject_pass_count)
  - `term_weights_json` (json)
  - `promotion_threshold` (decimal 5,2)
  - `conditional_threshold` (decimal 5,2, nullable)
  - `min_subjects_to_pass` (int, nullable)
  - `per_subject_pass_mark` (decimal 5,2, nullable)
  - `repeat_limit` (int)
  - `exclude_terminal_year` (boolean)
  - `is_active` (boolean)
  - `created_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at` (timestamps)

### `promotion_runs`
- **Purpose**: Logs execution batches of promotions.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `level` (varchar)
  - `run_by` (FK to `users`, cascade)
  - `status` (enum: draft, teacher_reviewed, approved, published)
  - `generated_at`, `approved_at`, `published_at` (timestamps)
  - `approved_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at` (timestamps)

### `student_promotion_records`
- **Purpose**: Holds computed decisions and override logs per student.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `promotion_run_id` (FK to `promotion_runs`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `from_class_id` (FK to `classes`, cascade)
  - `to_class_id` (FK to `classes`, nullable, nullOnDelete)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term1_score`, `term2_score`, `term3_score`, `computed_average` (decimal 5,2)
  - `method_used` (varchar)
  - `rule_snapshot_json` (json: frozen state of rules at run time)
  - `decision` (enum: promoted, conditional, repeat, bece_candidate, wassce_candidate)
  - `is_override` (boolean)
  - `override_reason` (text, nullable)
  - `decided_by` (FK to `users`, nullable, nullOnDelete)
  - `decided_at` (timestamp, nullable)
  - `created_at`, `updated_at` (timestamps)

### `student_repeat_history`
- **Purpose**: Counts repeat instances. Alerts admins when repeats exceed limits.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `class_id` (FK to `classes`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `repeat_count_at_this_class` (int)
  - `reason` (text, nullable)
  - `recorded_by` (FK to `users`, cascade)
  - `created_at`, `updated_at` (timestamps)

---

## 7. Staff & HR Records

### `staff`
- **Purpose**: Extends standard user profile with banking, professional, and SSNIT variables.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `user_id` (FK to `users`, cascade)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `staff_number` (varchar)
  - `designation` (varchar)
  - `department_id` (FK to `departments`, nullable, nullOnDelete)
  - `employment_type` (enum: permanent, contract, temporary)
  - `qualification`, `specialization`, `professional_cert` (varchars, nullable)
  - `date_joined`, `date_left`, `contract_start`, `contract_end` (dates)
  - `salary_grade`, `bank_name`, `bank_account`, `bank_branch`, `ssnit_number`, `tin_number` (nullable)
  - `emergency_contact_name`, `emergency_contact_phone`, `national_id_type`, `national_id_number` (nullable)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - `created_at`, `updated_at`, `deleted_at` (timestamps, SoftDeletes)
  - **Index**: Unique composite `[school_id, staff_number]`

### `staff_documents`
- **Purpose**: Mappings to PDFs of CVs, contracts, and certifications.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `staff_id` (FK to `staff`, cascade)
  - `document_type` (varchar)
  - `file_path` (varchar)
  - `uploaded_at` (datetime)
  - `expiry_date` (date, nullable)

### `staff_qualifications`
- **Purpose**: Academic degrees catalog.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `staff_id` (FK to `staff`, cascade)
  - `institution`, `qualification` (varchars)
  - `year_obtained` (int)
  - `certificate_path` (varchar, nullable)

### `staff_appraisals`
- **Purpose**: Evaluator scores.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `staff_id` (FK to `staff`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, nullable, nullOnDelete)
  - `appraiser_id` (FK to `users`, cascade)
  - `criteria` (json: metrics ratings)
  - `total_score` (decimal 5,2)
  - `grade`, `status` (varchars)
  - `comments` (text, nullable)
  - `created_at`, `updated_at` (timestamps)

---

## 8. Attendance & Operations

### `attendance_records`
- **Purpose**: Student daily attendance logs supporting manual/digital inputs.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `class_id` (FK to `classes`, cascade)
  - `stream_id` (FK to `streams`, nullable, nullOnDelete)
  - `student_id` (FK to `students`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, cascade)
  - `date` (date)
  - `status` (enum: present, absent, late, excused)
  - `arrival_time` (time, nullable)
  - `method` (enum: manual, qr, rfid, biometric, gps)
  - `late_minutes` (int)
  - `notes` (text, nullable)
  - `recorded_by` (FK to `users`, cascade)
  - `synced_from_offline` (boolean)
  - `created_at`, `updated_at` (timestamps)
  - **Index**: Unique composite `[school_id, student_id, date, term_id]`

---

## 9. Finance & Accounts

### `fee_structures`
- **Purpose**: Billing items mapping costs to specific years, terms, and classes.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, nullable, nullOnDelete)
  - `class_id` (FK to `classes`, nullable, nullOnDelete)
  - `name` (varchar)
  - `amount` (decimal 12,2)
  - `due_date` (date)
  - `is_mandatory` (boolean)

### `invoices`
- **Purpose**: Student invoices.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `campus_id` (FK to `campuses`, nullable, nullOnDelete)
  - `student_id` (FK to `students`, cascade)
  - `academic_year_id` (FK to `academic_years`, cascade)
  - `term_id` (FK to `terms`, cascade)
  - `invoice_number` (varchar)
  - `total_amount`, `amount_paid`, `balance` (decimal 12,2)
  - `status` (enum: pending, partial, paid, overdue, waived)
  - `due_date` (date)
  - `notes` (text, nullable)
  - `created_by` (FK to `users`, cascade)
  - **Index**: Unique composite `[school_id, invoice_number]`

### `payments`
- **Purpose**: Log of received fee payments.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `invoice_id` (FK to `invoices`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `amount` (decimal 12,2)
  - `payment_date` (date)
  - `method` (enum: cash, momo, bank_transfer, cheque, online)
  - `reference_number` (varchar, nullable)
  - `received_by` (FK to `users`, cascade)
  - `receipt_number` (varchar)
  - `notes` (text, nullable)
  - `gateway_response` (json, nullable)
  - `is_reversed` (boolean)
  - **Index**: Unique composite `[school_id, receipt_number]`

### `chart_of_accounts`
- **Purpose**: Double-entry ledger structure accounts.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `account_code`, `account_name` (varchars)
  - `account_type` (enum: Asset, Liability, Equity, Revenue, Expense)
  - `parent_id` (FK to self, nullOnDelete)
  - `is_active` (boolean)
  - **Index**: Unique composite `[school_id, account_code]`

---

## 10. School Website Builder

### `website_pages`
- **Purpose**: Static and dynamic sub-pages for the public school website.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `title`, `slug` (varchars)
  - `meta_description`, `og_image` (nullable)
  - `page_type` (enum: home, about, admissions, contact, news, events, gallery, custom)
  - `is_published`, `is_homepage` (booleans)
  - `published_at` (timestamp, nullable)
  - `display_order` (int)
  - `created_by`, `updated_by` (FK to `users`, nullable, nullOnDelete)
  - **Index**: Unique composite `[school_id, slug]`

### `page_revisions`
- **Purpose**: Versions of GrapesJS JSON blocks and rendered CSS/HTML.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `website_page_id` (FK to `website_pages`, cascade)
  - `revision_number` (int)
  - `html_content`, `css_content`, `components_json` (longText, nullable)
  - `is_current_draft`, `is_published` (booleans)
  - `published_at` (timestamp, nullable)
  - `published_by` (FK to `users`, nullable, nullOnDelete)
  - `notes` (text, nullable)
  - `created_by` (FK to `users`, cascade)

### `website_blocks`
- **Purpose**: Library of GrapesJS layout templates (e.g. Hero banners, contact forms).
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `name`, `slug` (varchars, unique)
  - `category` (Layout, Content, Media, Dynamic, Contact)
  - `html_template` (longText)
  - `preview_image`, `dynamic_source` (varchars, nullable)
  - `is_dynamic`, `is_active` (booleans)
  - `display_order` (int)

### `website_settings`
- **Purpose**: School site custom domains, colors, branding, logos, and scripts.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `site_name`, `site_tagline`, `logo_path`, `favicon_path` (nullable)
  - `primary_color`, `secondary_color`, `accent_color`, `text_color`, `bg_color` (varchars)
  - `heading_font`, `body_font` (varchars)
  - `social_facebook`, `social_twitter`, `social_instagram`, `social_youtube` (nullable)
  - `google_analytics_id`, `custom_header_scripts` (nullable)
  - `contact_address`, `contact_phone`, `contact_email`, `contact_map_embed` (nullable)
  - `is_published` (boolean)

---

## 11. Assignments & LMS

### `assignments`
- **Purpose**: Student homework tasks.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `school_id` (FK to `schools`, cascade)
  - `class_id` (FK to `classes`, cascade)
  - `stream_id` (FK to `streams`, nullable, nullOnDelete)
  - `subject_id` (FK to `subjects`, cascade)
  - `teacher_id` (FK to `users`, cascade)
  - `title` (varchar)
  - `description` (text, nullable)
  - `due_date` (datetime)
  - `max_marks` (decimal 8,2)
  - `is_active` (boolean)

### `assignment_submissions`
- **Purpose**: Submitted student tasks and grades.
- **Columns**:
  - `id` (bigint unsigned, PK)
  - `assignment_id` (FK to `assignments`, cascade)
  - `student_id` (FK to `students`, cascade)
  - `submitted_at` (datetime)
  - `marks_obtained` (decimal 8,2, nullable)
  - `status` (varchar: submitted, graded, late)

---

## 12. Supporting Operations & System Tables
All auxiliary operational and logging tables (including `library_*`, `inventory_*`, `hostel_*`, `transport_*`, `leave_*`, `payroll_*`, `messages`, `health_*`, `discipline_*`, `alumni_*`, `ai_*`, `documents`, `tickets`, `audit_logs`, `webhooks`, and `safeguarding_*`) are declared with full structural integrity. They strictly incorporate:
- `school_id` with foreign key constraints mapping back to `schools`.
- Localized relational references (e.g. `student_id` to `students`, `user_id` to `users`).
- Proper index mappings and audit logging configurations.
