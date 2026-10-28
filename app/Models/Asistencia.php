<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    // Campos necesarios para los reportes de atrasos, salidas anticipadas e inasistencias.
    protected $fillable = [
        'user_id',
        'tipo',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado',
    ];

    /**
     * Relación con el modelo User
     * Cada registro de asistencia pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para filtrar entradas atrasadas (después de las 9:30).
     */
    public function scopeAtrasos($query)
    {
        return $query->where('tipo', 'entrada')
                     ->whereTime('created_at', '>', '09:30:00');
    }

    /**
     * Scope para filtrar salidas anticipadas (antes de las 17:30).
     */
    public function scopeSalidasAnticipadas($query)
    {
        return $query->where('tipo', 'salida')
                     ->whereTime('created_at', '<', '17:30:00');
    }

    /**
     * Scope para verificar si un usuario asistió en un día específico.
     */
    public function scopeDelDia($query, $fecha, $userId)
    {
        return $query->where('user_id', $userId)
                     ->whereDate('created_at', $fecha);
    }
}
