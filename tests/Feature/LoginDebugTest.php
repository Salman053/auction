<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginDebugTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_attempt()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => UserRole::User->value,
        ]);

        $attempt = Auth::guard('user')->attempt([
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => UserRole::User->value,
            'suspended_at' => null,
        ]);

        $this->assertTrue($attempt, 'User login attempt failed');
        $this->assertTrue(Auth::guard('user')->check(), 'User guard check failed');
    }

    public function test_admin_login_attempt_as_user_should_fail()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $attempt = Auth::guard('user')->attempt([
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => UserRole::User->value,
            'suspended_at' => null,
        ]);

        $this->assertFalse($attempt, 'Admin login attempt as user should have failed but succeeded');
    }
}
