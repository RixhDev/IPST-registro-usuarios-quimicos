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

    public function test_no_se_puede_eliminar_al_administrador_principal()
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'status' => 'activo',
        ]);

        $this->actingAs(User::factory()->create(['status' => 'activo']), 'web');

        $response = $this->deleteJson('/api/usuarios/' . $admin->id);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Acción no permitida: El usuario administrador principal está protegido y no puede ser eliminado.',
            ]);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'email' => 'admin@admin.com']);
    }

}
