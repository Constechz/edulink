<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\GradingScale;
use App\Models\GradingScaleItem;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    public function run(): void
    {
        // For each school in the system, seed default GES/WAEC grading scales
        $schools = School::all();

        foreach ($schools as $school) {
            $this->seedScalesForSchool($school->id);
        }
    }

    public function seedScalesForSchool(int $schoolId): void
    {
        // 1. Creche & KG Standards-Based Scale
        $kgScale = GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES KG Standards-Based', 'level' => 'KG'],
            ['is_active' => true, 'is_default' => false]
        );

        $sbcItems = [
            ['grade' => 'Adv', 'min_score' => 80.00, 'max_score' => 100.00, 'grade_point' => 4.00, 'description' => 'Advanced', 'display_order' => 1],
            ['grade' => 'Prof', 'min_score' => 75.00, 'max_score' => 79.99, 'grade_point' => 3.00, 'description' => 'Proficient', 'display_order' => 2],
            ['grade' => 'Appr', 'min_score' => 70.00, 'max_score' => 74.99, 'grade_point' => 2.00, 'description' => 'Approaching Proficiency', 'display_order' => 3],
            ['grade' => 'Dev', 'min_score' => 65.00, 'max_score' => 69.99, 'grade_point' => 1.00, 'description' => 'Developing', 'display_order' => 4],
            ['grade' => 'Beg', 'min_score' => 0.00, 'max_score' => 64.99, 'grade_point' => 0.00, 'description' => 'Beginning', 'display_order' => 5],
        ];

        foreach ($sbcItems as $item) {
            GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $kgScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 2. Primary Standards-Based Scale
        $primaryScale = GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES Primary Standards-Based', 'level' => 'Primary'],
            ['is_active' => true, 'is_default' => false]
        );

        foreach ($sbcItems as $item) {
            GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $primaryScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 3. GES Basic School Scale (JHS)
        $basicScale = GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'GES JHS BECE Scale', 'level' => 'JHS'],
            ['is_active' => true, 'is_default' => true]
        );

        $basicItems = [
            ['grade' => '1', 'min_score' => 80.00, 'max_score' => 100.00, 'grade_point' => 1.00, 'description' => 'Highest/Excellent', 'display_order' => 1],
            ['grade' => '2', 'min_score' => 70.00, 'max_score' => 79.99, 'grade_point' => 2.00, 'description' => 'Very Good', 'display_order' => 2],
            ['grade' => '3', 'min_score' => 60.00, 'max_score' => 69.99, 'grade_point' => 3.00, 'description' => 'Good', 'display_order' => 3],
            ['grade' => '4', 'min_score' => 55.00, 'max_score' => 59.99, 'grade_point' => 4.00, 'description' => 'High Credit', 'display_order' => 4],
            ['grade' => '5', 'min_score' => 50.00, 'max_score' => 54.99, 'grade_point' => 5.00, 'description' => 'Credit', 'display_order' => 5],
            ['grade' => '6', 'min_score' => 45.00, 'max_score' => 49.99, 'grade_point' => 6.00, 'description' => 'Pass', 'display_order' => 6],
            ['grade' => '7', 'min_score' => 40.00, 'max_score' => 44.99, 'grade_point' => 7.00, 'description' => 'Pass', 'display_order' => 7],
            ['grade' => '8', 'min_score' => 35.00, 'max_score' => 39.99, 'grade_point' => 8.00, 'description' => 'Pass', 'display_order' => 8],
            ['grade' => '9', 'min_score' => 0.00, 'max_score' => 34.99, 'grade_point' => 9.00, 'description' => 'Fail', 'display_order' => 9],
        ];

        foreach ($basicItems as $item) {
            GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $basicScale->id, 'grade' => $item['grade']],
                $item
            );
        }

        // 4. WAEC SHS Scale
        $shsScale = GradingScale::updateOrCreate(
            ['school_id' => $schoolId, 'name' => 'WAEC SHS Standard', 'level' => 'SHS'],
            ['is_active' => true, 'is_default' => false]
        );

        $shsItems = [
            ['grade' => 'A1', 'min_score' => 75.00, 'max_score' => 100.00, 'grade_point' => 1.00, 'description' => 'Excellent', 'display_order' => 1],
            ['grade' => 'B2', 'min_score' => 70.00, 'max_score' => 74.99, 'grade_point' => 2.00, 'description' => 'Very Good', 'display_order' => 2],
            ['grade' => 'B3', 'min_score' => 65.00, 'max_score' => 69.99, 'grade_point' => 3.00, 'description' => 'Good', 'display_order' => 3],
            ['grade' => 'C4', 'min_score' => 60.00, 'max_score' => 64.99, 'grade_point' => 4.00, 'description' => 'Credit', 'display_order' => 4],
            ['grade' => 'C5', 'min_score' => 55.00, 'max_score' => 59.99, 'grade_point' => 5.00, 'description' => 'Credit', 'display_order' => 5],
            ['grade' => 'C6', 'min_score' => 50.00, 'max_score' => 54.99, 'grade_point' => 6.00, 'description' => 'Credit', 'display_order' => 6],
            ['grade' => 'D7', 'min_score' => 45.00, 'max_score' => 49.99, 'grade_point' => 7.00, 'description' => 'Pass', 'display_order' => 7],
            ['grade' => 'E8', 'min_score' => 40.00, 'max_score' => 44.99, 'grade_point' => 8.00, 'description' => 'Pass', 'display_order' => 8],
            ['grade' => 'F9', 'min_score' => 0.00, 'max_score' => 39.99, 'grade_point' => 9.00, 'description' => 'Fail', 'display_order' => 9],
        ];

        foreach ($shsItems as $item) {
            GradingScaleItem::updateOrCreate(
                ['grading_scale_id' => $shsScale->id, 'grade' => $item['grade']],
                $item
            );
        }
    }
}
