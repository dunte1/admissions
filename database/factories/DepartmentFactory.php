<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = \App\Models\Department::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name' => $this->faker->randomElement([
                'Business and Management',
                'Information Technology',
                'Health Sciences',
                'Media and Communication',
                'Hospitality and Tourism',
            ]),
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}