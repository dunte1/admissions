<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = \App\Models\Program::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => $this->faker->randomElement([
                'Certificate in Business Management',
                'Diploma in Information Technology',
                'Degree in Nursing',
                'Diploma in Journalism',
                'Certificate in Catering',
            ]),
            'code' => strtoupper($this->faker->unique()->lexify('????')),
            'description' => $this->faker->paragraph(),
            'department_id' => Department::factory(),
            'duration_years' => $this->faker->randomElement([1, 2, 3, 4]),
            'level' => $this->faker->randomElement(['certificate', 'diploma', 'degree']),
            'tuition_per_year' => $this->faker->numberBetween(30000, 200000),
            'capacity' => $this->faker->numberBetween(20, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function degree(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'degree',
            'duration_years' => 4,
        ]);
    }

    public function diploma(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'diploma',
            'duration_years' => 2,
        ]);
    }

    public function certificate(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'certificate',
            'duration_years' => 1,
        ]);
    }
}