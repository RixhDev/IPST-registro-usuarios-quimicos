<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AsistenciaTest extends TestCase
{
	use RefreshDatabase;

	public function test_registrar_entrada_devuelve_mensaje_correcto(): void
	{
		$usuario = User::factory()->create();
		Sanctum::actingAs($usuario);

		$response = $this->postJson('/api/asistencia/entrada');

		$response->assertOk()
			->assertJson([
				'mensaje' => '[+] Entrada registrada.',
			]);

		$this->assertDatabaseHas('asistencias', [
			'user_id' => $usuario->id,
			'tipo' => 'entrada',
		]);
	}

	public function test_registrar_salida_devuelve_mensaje_correcto(): void
	{
		$usuario = User::factory()->create();
		Sanctum::actingAs($usuario);

		$response = $this->postJson('/api/asistencia/salida');

		$response->assertOk()
			->assertJson([
				'mensaje' => '[+] Salida registrada.',
			]);

		$this->assertDatabaseHas('asistencias', [
			'user_id' => $usuario->id,
			'tipo' => 'salida',
		]);
	}
}
