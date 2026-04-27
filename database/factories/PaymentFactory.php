<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = \App\Models\Payment::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'application_id' => Application::factory(),
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(500, 5000),
            'payment_type' => $this->faker->randomElement(['admission_fee', 'commitment_fee']),
            'payment_method' => $this->faker->randomElement(['mpesa', 'paypal', 'bank_transfer']),
            'transaction_id' => $this->faker->uuid(),
            'phone_number' => '254' . $this->faker->numberBetween(700000000, 799999999),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'paid_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function ($payment) {
            if (!$payment->school_id && $payment->application) {
                $payment->school_id = $payment->application->school_id;
            }
        });
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => now(),
            'mpesa_receipt' => 'M' . $this->faker->numberBetween(100000, 999999),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failure_reason' => 'Payment cancelled by user',
        ]);
    }

    public function mpesa(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'mpesa',
        ]);
    }

    public function manualMpesa(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'manual_mpesa',
            'status' => 'manual_pending',
        ]);
    }
}