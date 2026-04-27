<?php

namespace Tests\Unit\Models;

use App\Models\School;
use App\Models\User;
use App\Models\Application;
use App\Models\Program;
use App\Models\Payment;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_has_correct_fillable_attributes()
    {
        $school = new School();
        $this->assertContains('name', $school->getFillable());
        $this->assertContains('code', $school->getFillable());
        $this->assertContains('status', $school->getFillable());
    }

    public function test_school_can_have_users()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals(1, $school->users()->count());
    }

    public function test_school_can_have_applications()
    {
        $school = School::factory()->create();
        $application = Application::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals(1, $school->applications()->count());
    }

    public function test_school_can_have_programs()
    {
        $school = School::factory()->create();
        $program = Program::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals(1, $school->programs()->count());
    }

    public function test_school_active_scope_works()
    {
        $activeSchool = School::factory()->create(['status' => 'active']);
        School::factory()->create(['status' => 'inactive']);
        
        $activeSchools = School::active()->get();
        
        $this->assertTrue($activeSchools->contains($activeSchool));
        $this->assertEquals(1, $activeSchools->count());
    }

    public function test_school_can_check_subscription_status()
    {
        $school = School::factory()->create([
            'status' => 'active',
            'subscription_status' => 'active',
        ]);
        
        $this->assertTrue($school->isActive());
    }

    public function test_school_can_get_current_id()
    {
        $school = School::factory()->create(['id' => 123]);
        School::setCurrentId(123);
        
        $this->assertEquals(123, School::getCurrentId());
    }
}