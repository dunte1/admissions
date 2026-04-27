<?php

namespace Tests\Unit\Models;

use App\Models\Payment;
use App\Models\Application;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_has_correct_fillable_attributes()
    {
        $payment = new Payment();
        $this->assertContains('school_id', $payment->getFillable());
        $this->assertContains('application_id', $payment->getFillable());
        $this->assertContains('amount', $payment->getFillable());
        $this->assertContains('status', $payment->getFillable());
    }

    public function test_payment_belongs_to_application()
    {
        $application = Application::factory()->create();
        $payment = Payment::factory()->create(['application_id' => $application->id]);
        
        $this->assertEquals($application->id, $payment->application->id);
    }

    public function test_payment_can_be_pending()
    {
        $payment = Payment::factory()->pending()->create();
        
        $this->assertTrue($payment->isPending());
    }

    public function test_payment_can_be_completed()
    {
        $payment = Payment::factory()->completed()->create();
        
        $this->assertTrue($payment->isCompleted());
    }

    public function test_payment_can_be_failed()
    {
        $payment = Payment::factory()->failed()->create();
        
        $this->assertTrue($payment->isFailed());
    }

    public function test_payment_scope_pending_works()
    {
        Payment::factory()->pending()->create();
        Payment::factory()->completed()->create();
        
        $pending = Payment::pending()->get();
        
        $this->assertEquals(1, $pending->count());
    }

    public function test_payment_scope_completed_works()
    {
        Payment::factory()->pending()->create();
        $completed = Payment::factory()->completed()->create();
        
        $completedPayments = Payment::completed()->get();
        
        $this->assertEquals(1, $completedPayments->count());
    }

    public function test_payment_can_mark_as_completed()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);
        
        $payment->markAsCompleted('M123456');
        
        $this->assertEquals('completed', $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertEquals('M123456', $payment->mpesa_receipt);
    }

    public function test_payment_can_mark_as_failed()
    {
        $payment = Payment::factory()->create(['status' => 'pending']);
        
        $payment->markAsFailed('Insufficient funds');
        
        $this->assertEquals('failed', $payment->status);
        $this->assertEquals('Insufficient funds', $payment->failure_reason);
    }

    public function test_payment_can_generate_idempotency_key()
    {
        $key = Payment::generateIdempotencyKey('123', '254700000000', 'mpesa');
        
        $this->assertNotNull($key);
        $this->assertEquals(64, strlen($key));
    }

    public function test_payment_can_generate_receipt_number()
    {
        $payment = Payment::factory()->create(['receipt_number' => null]);
        
        $receipt = $payment->generateReceiptNumber();
        
        $this->assertNotNull($receipt);
        $this->assertStringStartsWith('RCP-', $receipt);
    }

    public function test_payment_get_status_badge_class_returns_correct_class()
    {
        $pending = Payment::factory()->create(['status' => 'pending']);
        $completed = Payment::factory()->create(['status' => 'completed']);
        $failed = Payment::factory()->create(['status' => 'failed']);
        
        $this->assertStringContainsString('yellow', $pending->getStatusBadgeClass());
        $this->assertStringContainsString('green', $completed->getStatusBadgeClass());
        $this->assertStringContainsString('red', $failed->getStatusBadgeClass());
    }
}