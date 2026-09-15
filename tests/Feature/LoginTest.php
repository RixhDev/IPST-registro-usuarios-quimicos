<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
	use RefreshDatabase;

	public function test_usuario_puede_iniciar_sesion_con_credenciales_validas(): void
	{
		$usuario = User::factory()->create([
			'email' => 'usuario@example.com',
			'password' => Hash::make('password123'),
		]);

		$response = $this->post('/login', [
			'email' => 'usuario@example.com',
			'password' => 'password123',
		]);

		$response->assertRedirect(route('welcome'));
		$this->assertAuthenticatedAs($usuario);
	}

	public function test_usuario_no_puede_iniciar_sesion_con_credenciales_invalidas(): void
	{
		User::factory()->create([
			'email' => 'usuario@example.com',
			'password' => Hash::make('password123'),
		]);

		$response = $this->from('/')->post('/login', [
			'email' => 'usuario@example.com',
			'password' => 'password-incorrecta',
		]);

		$response->assertRedirect('/');
		$response->assertSessionHasErrors('email');
		$this->assertGuest();
	}

	public function test_usuario_creado_desde_gestion_de_usuarios_puede_iniciar_sesion(): void
	{
		$this->actingAs(User::factory()->create(), 'web');

		$this->postJson('/api/usuarios', [
			'name' => 'Usuario Registrado',
			'email' => 'registrado@example.com',
			'password' => 'password123',
			'status' => 'activo',
		])->assertOk();

		$this->post('/logout');

		$response = $this->from('/')->post('/login', [
			'email' => 'registrado@example.com',
			'password' => 'password123',
		]);

		$response->assertRedirect(route('welcome'));
		$this->assertAuthenticatedAs(User::where('email', 'registrado@example.com')->first());
	}
}
