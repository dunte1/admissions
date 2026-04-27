<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use App\Models\Application;
use App\Models\Program;
use App\Models\Intake;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'school_id' => School::factory(),
            'student_id' => Student::factory(),
            'intake_id' => Intake::factory(),
            'application_number' => $this->faker->unique()->uuid(),
            'program_id' => Program::factory(),
            'program_choice_2' => null,
            'program_choice_3' => null,
            'status' => 'pending',
            'current_step' => 'personal',
            'form_data' => [],
            'total_score' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'review_notes' => null,
            'submitted_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
            'review_notes' => $this->faker->sentence(),
        ]);
    }
}