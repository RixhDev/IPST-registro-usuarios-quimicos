<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia; // Asegúrate de tener este modelo creado
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * 1. Reporte de Atrasos
     */
    public function atrasos(Request $request)
    {
        $query = Asistencia::with('user');

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        // Usamos el scope del modelo o mantenemos tu lógica de hora de entrada
        $atrasos = $query->where('hora_entrada', '>', '08:30:00')
                         ->orderBy('fecha', 'desc')
                         ->get();

        return response()->json([
            'status' => 'success',
            'data' => $atrasos
        ]);
    }

    /**
     * 2. Reporte de Salidas Anticipadas
     */
    public function salidasAnticipadas(Request $request)
    {
        $query = Asistencia::with('user');

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $salidasAnticipadas = $query->where('hora_salida', '<', '18:00:00')
                                    ->whereNotNull('hora_salida')
                                    ->orderBy('fecha', 'desc')
                                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $salidasAnticipadas
        ]);
    }

    /**
     * 3. Reporte de Inasistencias
     */
    public function inasistencias(Request $request)
    {
        $query = Asistencia::with('user');

        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $inasistencias = $query->where('estado', 'ausente')
                               ->orderBy('fecha', 'desc')
                               ->get();

        return response()->json([
            'status' => 'success',
            'data' => $inasistencias
        ]);
    }

    /**
     * 4. Exportar Reporte Consolidado con Fecha Actual (Ideal para el profesor)
     */
    public function exportarReporteGeneral(Request $request)
    {
        $fechaActual = Carbon::now()->format('Y-m-d_H-i-s');
        $fechaLegible = Carbon::now()->format('d/m/Y H:i');

        // Obtenemos los datos agrupados
        $atrasos = Asistencia::with('user')->where('hora_entrada', '>', '08:30:00')->get();
        $salidasAnticipadas = Asistencia::with('user')->where('hora_salida', '<', '18:00:00')->whereNotNull('hora_salida')->get();
        $inasistencias = Asistencia::with('user')->where('estado', 'ausente')->get();

        $nombreArchivo = "reporte_general_asistencias_{$fechaActual}.csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$nombreArchivo",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($atrasos, $salidasAnticipadas, $inasistencias, $fechaLegible) {
            $file = fopen('php://output', 'w');
            // BOM para compatibilidad con tildes en Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezado
            fputcsv($file, ['SISTEMA DE CONTROL DE ASISTENCIA Y GESTIÓN DE PERSONAL']);
            fputcsv($file, ['Fecha de Emisión del Reporte: ' . $fechaLegible]);
            fputcsv($file, ['---']);

            // SECCIÓN ATRASOS
            fputcsv($file, ['=== ATRASOS ===']);
            fputcsv($file, ['ID', 'Nombre', 'Correo', 'Fecha', 'Hora Entrada']);
            foreach ($atrasos as $row) {
                fputcsv($file, [$row->user_id, $row->user->name ?? 'N/A', $row->user->email ?? 'N/A', $row->fecha, $row->hora_entrada]);
            }
            fputcsv($file, []);

            // SECCIÓN SALIDAS ANTICIPADAS
            fputcsv($file, ['=== SALIDAS ANTICIPADAS ===']);
            fputcsv($file, ['ID', 'Nombre', 'Correo', 'Fecha', 'Hora Salida']);
            foreach ($salidasAnticipadas as $row) {
                fputcsv($file, [$row->user_id, $row->user->name ?? 'N/A', $row->user->email ?? 'N/A', $row->fecha, $row->hora_salida]);
            }
            fputcsv($file, []);

            // SECCIÓN INASISTENCIAS
            fputcsv($file, ['=== INASISTENCIAS ===']);
            fputcsv($file, ['ID', 'Nombre', 'Correo', 'Fecha', 'Estado']);
            foreach ($inasistencias as $row) {
                fputcsv($file, [$row->user_id, $row->user->name ?? 'N/A', $row->user->email ?? 'N/A', $row->fecha, $row->estado]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}