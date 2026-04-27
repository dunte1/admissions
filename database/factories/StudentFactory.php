<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = \App\Models\Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'id_number' => $this->faker->unique()->numerify('##########'),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'county' => $this->faker->state(),
            'sub_county' => $this->faker->city(),
            'nationality' => 'Kenyan',
        ];
    }
}