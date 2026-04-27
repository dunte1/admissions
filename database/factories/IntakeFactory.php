<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class IntakeFactory extends Factory
{
    protected $model = \App\Models\Intake::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => $this->faker->randomElement(['January 2026', 'May 2026', 'September 2026']),
            'code' => strtoupper($this->faker->unique()->lexify('INT-????')),
            'year' => 2026,
            'semester' => $this->faker->randomElement(['1', '2', '3']),
            'application_start_date' => now(),
            'application_end_date' => now()->addMonths(2),
            'is_current' => true,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function notCurrent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_current' => false,
        ]);
    }
}