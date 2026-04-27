<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'code' => strtoupper($this->faker->unique()->lexify('????')),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'county' => $this->faker->state(),
            'town' => $this->faker->city(),
            'status' => 'active',
            'subscription_status' => 'active',
            'trial_ends_at' => now()->addDays(14),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
            'subscription_status' => 'suspended',
        ]);
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);
    }
}