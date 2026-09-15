<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GestionUsuarios extends TestCase
{
	use RefreshDatabase;

	public function test_crear_usuario_devuelve_200_y_usuario(): void
	{
		$this->actingAs(User::factory()->create(), 'web');

		$response = $this->postJson('/api/usuarios', [
			'name' => 'Nuevo Usuario',
			'email' => 'nuevo@example.com',
			'password' => 'password123',
			'status' => 'activo',
		]);

		$response->assertOk()
			->assertJsonStructure([
				'mensaje',
				'usuario' => ['id', 'name', 'email', 'status'],
			])
			->assertJsonPath('usuario.name', 'Nuevo Usuario')
			->assertJsonPath('usuario.email', 'nuevo@example.com');

		$this->assertDatabaseHas('users', [
			'email' => 'nuevo@example.com',
			'name' => 'Nuevo Usuario',
			'status' => 'activo',
		]);
	}

	public function test_modificar_usuario_devuelve_200_y_aplica_cambios(): void
	{
		$this->actingAs(User::factory()->create(), 'web');
		$usuario = User::factory()->create([
			'name' => 'Nombre Original',
			'email' => 'original@example.com',
		]);

		$response = $this->putJson('/api/usuarios/'.$usuario->id, [
			'name' => 'Nombre Actualizado',
			'email' => 'actualizado@example.com',
		]);

		$response->assertOk()
			->assertJsonStructure([
				'mensaje',
				'usuario' => ['id', 'name', 'email', 'status'],
			])
			->assertJsonPath('usuario.name', 'Nombre Actualizado')
			->assertJsonPath('usuario.email', 'actualizado@example.com');

		$this->assertDatabaseHas('users', [
			'id' => $usuario->id,
			'name' => 'Nombre Actualizado',
			'email' => 'actualizado@example.com',
		]);
	}

	public function test_eliminar_usuario_devuelve_200_y_elimina_usuario(): void
	{
		$this->actingAs(User::factory()->create(), 'web');
		$usuario = User::factory()->create();

		$response = $this->deleteJson('/api/usuarios/'.$usuario->id);

		$response->assertOk()
			->assertJson([
				'mensaje' => '[+] Usuario eliminado correctamente.',
			]);

		$this->assertDatabaseMissing('users', [
			'id' => $usuario->id,
		]);
	}
}
