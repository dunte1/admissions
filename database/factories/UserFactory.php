<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'role' => 'student',
            'school_id' => null,
            'is_active' => true,
            'verification_token' => Str::random(10),
            'otp_code' => null,
            'otp_expires_at' => null,
            'preferred_language' => 'en',
            'dark_mode' => false,
            'photo' => null,
            'user_settings' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSchool(?School $school = null): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $school?->id ?? School::factory(),
        ]);
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('super_admin');
        });
    }

    public function admin(?School $school = null): static
    {
        return $this->afterCreating(function (User $user) use ($school) {
            if ($school) {
                $user->school_id = $school->id;
                $user->save();
            }
            $user->assignRole('admin');
        });
    }

    public function student(?School $school = null): static
    {
        return $this->afterCreating(function (User $user) use ($school) {
            if ($school) {
                $user->school_id = $school->id;
                $user->save();
            }
            $user->assignRole('student');
        });
    }
}
