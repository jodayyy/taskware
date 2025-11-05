<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_can_register(): void
	{
		$response = $this->post('/register', [
			'username' => 'testuser',
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$response->assertRedirect('/login');
		$response->assertSessionHas('success');
		
		$this->assertDatabaseHas('users', ['username' => 'testuser']);
	}

	public function test_user_can_login(): void
	{
		$user = User::factory()->create([
			'username' => 'testuser',
			'password' => bcrypt('password123'),
		]);

		$response = $this->post('/login', [
			'username' => 'testuser',
			'password' => 'password123',
		]);

		$response->assertRedirect('/dashboard');
		$this->assertAuthenticatedAs($user);
	}

	public function test_user_cannot_login_with_invalid_credentials(): void
	{
		$user = User::factory()->create([
			'username' => 'testuser',
			'password' => bcrypt('password123'),
		]);

		$response = $this->post('/login', [
			'username' => 'testuser',
			'password' => 'wrongpassword',
		]);

		$response->assertSessionHasErrors('username');
		$this->assertGuest();
	}

	public function test_user_can_logout(): void
	{
		$user = User::factory()->create();

		$response = $this->actingAs($user)->post('/logout');

		$response->assertRedirect('/');
		$this->assertGuest();
	}
}

