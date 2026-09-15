<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportesTest extends TestCase
{
	use RefreshDatabase;

	public function test_reporte_de_atrasos_devuelve_200_y_estructura_esperada(): void
	{
		$usuario = User::factory()->create();
		Sanctum::actingAs($usuario);
		$this->crearAsistencia($usuario, 'entrada', '10:00:00');

		$response = $this->getJson('/api/reportes/atrasos');

		$response->assertOk()
			->assertJsonStructure([
				'*' => [
					'id',
					'user_id',
					'tipo',
					'created_at',
					'updated_at',
					'user' => ['id', 'name', 'email'],
				],
			]);
	}

	public function test_reporte_de_salidas_anticipadas_devuelve_200_y_estructura_esperada(): void
	{
		$usuario = User::factory()->create();
		Sanctum::actingAs($usuario);
		$this->crearAsistencia($usuario, 'salida', '17:00:00');

		$response = $this->getJson('/api/reportes/salidas');

		$response->assertOk()
			->assertJsonStructure([
				'*' => [
					'id',
					'user_id',
					'tipo',
					'created_at',
					'updated_at',
					'user' => ['id', 'name', 'email'],
				],
			]);
	}

	public function test_reporte_de_inasistencias_devuelve_200_y_estructura_esperada(): void
	{
		$usuarioAutenticado = User::factory()->create();
		$usuarioAusente = User::factory()->create();
		Sanctum::actingAs($usuarioAutenticado);
		$this->crearAsistencia($usuarioAutenticado, 'entrada', '09:00:00');

		$response = $this->getJson('/api/reportes/inasistencias');

		$response->assertOk()
			->assertJsonStructure([
				'*' => ['id', 'name', 'email'],
			])
			->assertJsonFragment(['id' => $usuarioAusente->id]);
	}

	private function crearAsistencia(User $usuario, string $tipo, string $hora): void
	{
		$fechaHora = Carbon::today()->setTimeFromTimeString($hora);
		$asistencia = new Asistencia([
			'user_id' => $usuario->id,
			'tipo' => $tipo,
		]);
		$asistencia->created_at = $fechaHora;
		$asistencia->updated_at = $fechaHora;
		$asistencia->save();
	}
}
