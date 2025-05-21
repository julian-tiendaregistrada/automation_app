<?php

namespace Vendor\ExportSales\Http\Controllers;

use App\Jobs\ProcessExportTask;
use App\Models\Category;
use App\Models\ExportTask;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vendor\ExportSales\Http\Requests\ExportSalesRequest;

class ExportSalesController
{
    public function export(ExportSalesRequest $request): JsonResponse
    {
        $task = ExportTask::create([
            'user_id' => $request->user()->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
        ]);

        ProcessExportTask::dispatch($task)->onQueue('exports');

        return response()->json([
            'success' => true,
            'message' => 'Exportación iniciada. Recibirás una notificación cuando esté lista.',
            'task_id' => $task->id,
        ]);
    }

    public function getTasks(Request $request): JsonResponse
    {
        $tasks = ExportTask::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($tasks);
    }

    public function getCategories(): JsonResponse
    {
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }

    public function getSubcategories(Request $request): JsonResponse
    {
        $categoryId = $request->input('category');

        $subcategories = Subcategory::where('category_id', $categoryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }

    public function analyzeExport(Request $request, $taskId): JsonResponse
    {
        $task = ExportTask::findOrFail($taskId);

        if ($task->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        if ($task->status !== 'completed' || ! $task->file_path) {
            return response()->json(['success' => false, 'message' => 'El archivo no está disponible'], 400);
        }

        $filePath = storage_path('app/'.$task->file_path);
        if (! file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'Archivo no encontrado'], 404);
        }

        try {
            $response = Http::timeout(60)
                ->attach('file', fopen($filePath, 'r'), basename($filePath))
                ->post(config('services.analyzer.url').'/analyze');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            // Error devuelto por el microservicio
            return response()->json([
                'success' => false,
                'message' => 'Error del servicio de análisis: '.($response->json('message') ?? 'Respuesta inválida'),
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error al analizar el archivo: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al analizar el archivo: '.$e->getMessage(),
            ], 500);
        }
    }
}
