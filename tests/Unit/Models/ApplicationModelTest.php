<?php

namespace Tests\Unit\Models;

use App\Models\Application;
use App\Models\User;
use App\Models\School;
use App\Models\Program;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_has_correct_fillable_attributes()
    {
        $application = new Application();
        $this->assertContains('user_id', $application->getFillable());
        $this->assertContains('school_id', $application->getFillable());
        $this->assertContains('program_id', $application->getFillable());
        $this->assertContains('status', $application->getFillable());
    }

    public function test_application_belongs_to_school()
    {
        $school = School::factory()->create();
        $application = Application::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals($school->id, $application->school->id);
    }

    public function test_application_belongs_to_user()
    {
        $user = User::factory()->create();
        $application = Application::factory()->create(['user_id' => $user->id]);
        
        $this->assertEquals($user->id, $application->user->id);
    }

    public function test_application_can_have_payments()
    {
        $application = Application::factory()->create();
        $payment = \App\Models\Payment::factory()->create(['application_id' => $application->id]);
        
        $this->assertEquals(1, $application->payments()->count());
    }

    public function test_application_can_be_draft()
    {
        $application = Application::factory()->draft()->create();
        
        $this->assertTrue($application->isDraft());
        $this->assertEquals('draft', $application->status);
    }

    public function test_application_can_be_pending()
    {
        $application = Application::factory()->create(['status' => 'pending']);
        
        $this->assertTrue($application->isPending());
    }

    public function test_application_can_be_approved()
    {
        $application = Application::factory()->approved()->create();
        
        $this->assertTrue($application->isApproved());
    }

    public function test_application_can_be_rejected()
    {
        $application = Application::factory()->rejected()->create();
        
        $this->assertTrue($application->isRejected());
    }

    public function test_application_generates_unique_number()
    {
        $number = Application::generateNumber();
        
        $this->assertNotNull($number);
        $this->assertStringStartsWith('APP', $number);
    }

    public function test_application_scope_pending_works()
    {
        Application::factory()->create(['status' => 'pending']);
        Application::factory()->create(['status' => 'approved']);
        
        $pending = Application::where('status', 'pending')->get();
        
        $this->assertEquals(1, $pending->count());
    }

    public function test_application_scope_approved_works()
    {
        Application::factory()->create(['status' => 'pending']);
        Application::factory()->approved()->create();
        
        $approved = Application::where('status', 'approved')->get();
        
        $this->assertEquals(1, $approved->count());
    }
}