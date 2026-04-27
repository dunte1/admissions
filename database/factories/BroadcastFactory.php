<?php

namespace Database\Factories;

use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BroadcastFactory extends Factory
{
    protected $model = Broadcast::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'school_id' => null,
            'title' => fake()->sentence(4),
            'message' => fake()->paragraph(2),
            'type' => fake()->randomElement(['in_app', 'email', 'sms', 'all']),
            'targeting' => [
                'schools' => 'all',
                'roles' => [],
                'application_status' => [],
            ],
            'status' => 'draft',
            'scheduled_at' => null,
            'sent_at' => null,
            'total_recipients' => 0,
            'sent_count' => 0,
            'delivered_count' => 0,
            'failed_count' => 0,
            'is_test' => false,
        ];
    }

    public function inApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in_app',
        ]);
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'email',
        ]);
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'sms',
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'scheduled_at' => fake()->dateTimeBetween('+1 hour', '+1 week'),
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $attributes['total_recipients'] ?? 10,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    public function test(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_test' => true,
            'total_recipients' => 1,
        ]);
    }

    public function forSchool(int $schoolId): static
    {
        return $this->state(fn (array $attributes) => [
            'school_id' => $schoolId,
        ]);
    }

    public function byUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
