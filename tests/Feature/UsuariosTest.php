<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_listar_usuarios_activos()
    {
        $this->actingAs(User::factory()->create(['status' => 'inactivo']), 'web');
        User::factory()->create(['status' => 'activo']);

        $response = $this->getJson('/api/usuarios?estado=activo');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'estado',
                    'total',
                    'usuarios'
                ]);
    }

    public function test_listar_usuarios_estado_invalido()
    {
        $this->actingAs(User::factory()->create(['status' => 'inactivo']), 'web');
        $response = $this->getJson('/api/usuarios?estado=otro');

        $response->assertStatus(400)
                ->assertJson([
                    'error' => '[!] Estado inválido -> Usar: (activo, inactivo, todos.)'
                ]);
    }

    public function test_listar_usuarios_sin_resultados()
    {
        $this->actingAs(User::factory()->create(['status' => 'inactivo']), 'web');
        $response = $this->getJson('/api/usuarios?estado=activo');
        $response->assertStatus(404)
                ->assertJson([
                    'estado' => 'activo',
                    'total' => 0 ,
                    'usuarios' => [],
                    'mensaje' => '[!] No se encontraron empleados con el estado solicitado.'
                ]);
    }

}
