<?php

namespace App\Jobs;

use App\Models\ExportTask;
use App\Notifications\ExportTaskCompleted;
use App\Notifications\ExportTaskFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ProcessExportTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;

    public $timeout = 3600; // 1 hora

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(ExportTask $task)
    {
        $this->task = $task;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando exportación para tarea: {$this->task->id}");

        $this->task->markAsProcessing();

        try {
            // Actualizar progreso
            $this->task->updateProgress(10);

            // Construir y ejecutar comando BCP
            $filePath = $this->executeBcpCommand();

            // Actualizar progreso
            $this->task->updateProgress(90);

            // Marcar como completado
            $this->task->markAsCompleted($filePath);

            // Notificar al usuario
            $this->task->user->notify(new ExportTaskCompleted($this->task));

            Log::info("Exportación completada: {$this->task->id}");

        } catch (\Exception $e) {
            Log::error('Error en exportación: '.$e->getMessage());

            $this->task->markAsFailed($e->getMessage());
            $this->task->user->notify(new ExportTaskFailed($this->task));

            throw $e;
        }
    }

    private function executeBcpCommand()
    {
        $config = [
            'host' => config('services.sqlserver.host'),
            'user' => config('services.sqlserver.user'),
            'password' => config('services.sqlserver.password'),
        ];

        // Crear directorio si no existe
        $exportPath = storage_path('app/exports');
        if (! file_exists($exportPath)) {
            mkdir($exportPath, 0755, true);
        }

        // Generar nombre de archivo único
        $filename = sprintf(
            'export_%s_%s_%s.csv',
            $this->task->id,
            $this->task->user_id,
            now()->format('Y-m-d_H-i-s')
        );
        $outputPath = $exportPath.'/'.$filename;

        // Construir query
        $query = $this->buildQuery();

        // Construir comando BCP
        $bcpCommand = sprintf(
            'bcp "%s;" queryout "%s" -c -C 65001 -t; -S %s -U %s -P %s',
            $query,
            $outputPath,
            $config['host'],
            $config['user'],
            $config['password']
        );

        // Ejecutar comando
        $result = Process::timeout(3600)->run($bcpCommand);

        if (! $result->successful()) {
            throw new \Exception('Error ejecutando BCP: '.$result->errorOutput());
        }

        // Retornar la ruta relativa para guardar en la base de datos
        return 'exports/'.$filename;
    }

    private function buildQuery()
    {
        return sprintf(
            "SELECT hv.[Id], hv.[Fecha], hv.[UniqueID], hv.[CodigoDeBarras],
             hv.[Unidades], hv.[Precio], p.[DescripcionLargaProducto],
             p.[Peso_Volumen], p.[Familia], p.[Categoria], p.[SubCategoria],
             p.[Marca], p.[NombreDeProductor], p.[Unidad], t.Departamento,
             ad.[Guid], ad.[UnitsMean], ad.[PriceMean]
             FROM [FLX_Rpt].[dbo].[TR_TAB_OLAP_HVentas] hv
             INNER JOIN [FLX_Rpt].[dbo].[TBLvProductos] p
                ON hv.[CodigoDeBarras] = p.[CodigoDeBarras]
             INNER JOIN [FLX_Rpt].[dbo].[TBLvTiendas] t
                ON hv.[Id] = t.[ID]
             LEFT JOIN [FLX_Rpt].[dbo].[TR_TAB_Anomaly_Detection] ad
                ON hv.[CodigoDeBarras] = ad.[CodigoBarra]
                AND hv.[UniqueID] = ad.[Unique_id]
             WHERE hv.[Fecha] BETWEEN '%s' AND '%s'
             AND hv.[CodigoCategoria] = '%s'",
            $this->task->start_date->format('Y-m-d'),
            $this->task->end_date->format('Y-m-d'),
            str_replace("'", "''", $this->task->category) // Escapar comillas simples
        );
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Job falló para tarea: {$this->task->id}", [
            'error' => $exception->getMessage(),
        ]);

        $this->task->markAsFailed('Error crítico: '.$exception->getMessage());
        $this->task->user->notify(new ExportTaskFailed($this->task));
    }
}
