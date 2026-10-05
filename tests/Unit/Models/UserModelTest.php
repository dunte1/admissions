<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\School;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_fillable_attributes()
    {
        $user = new User();
        $this->assertContains('first_name', $user->getFillable());
        $this->assertContains('last_name', $user->getFillable());
        $this->assertContains('email', $user->getFillable());
        $this->assertContains('school_id', $user->getFillable());
    }

    public function test_user_belongs_to_school()
    {
        $school = School::factory()->create();
        $user = User::factory()->create(['school_id' => $school->id]);
        
        $this->assertEquals($school->id, $user->school->id);
    }

    public function test_user_can_be_admin()
    {
        $user = User::factory()->admin()->create();
        
        $this->assertTrue($user->isAdmin());
    }

    public function test_user_can_be_student()
    {
        $user = User::factory()->student()->create();
        
        $this->assertTrue($user->isStudent());
    }

    public function test_user_can_be_super_admin()
    {
        $user = User::factory()->superAdmin()->create();
        
        $this->assertTrue($user->isSuperAdmin());
    }

    public function test_user_can_check_roles()
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('student'));
    }

    public function test_user_can_check_any_roles()
    {
        $admin = User::factory()->admin()->create();
        
        $this->assertTrue($admin->hasAnyRole(['admin', 'super_admin']));
        $this->assertFalse($admin->hasAnyRole(['student', 'reviewer']));
    }

    public function test_user_full_name_returns_combined_name()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
        $this->assertEquals('John Doe', $user->fullName());
    }

    public function test_user_can_generate_otp()
    {
        $user = User::factory()->create();
        $otp = $user->generateOTP();
        
        $this->assertNotNull($otp);
        $this->assertEquals(6, strlen($otp));
    }

    public function test_user_can_clear_otp()
    {
        $user = User::factory()->create();
        $user->generateOTP();
        
        $this->assertNotNull($user->otp_code);
        
        $user->clearOTP();
        
        $this->assertNull($user->otp_code);
    }

    public function test_user_can_verify_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'is_verified' => false,
        ]);
        
        $user->markAsVerified();
        
        $this->assertTrue($user->is_verified);
        $this->assertFalse($user->is_first_login);
    }

    public function test_user_avatar_url_returns_default()
    {
        $user = User::factory()->create(['photo' => null]);
        
        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
    }

    public function test_user_initials_returns_first_letters()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
        $this->assertEquals('JD', $user->initials);
    }
}