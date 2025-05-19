<?php

namespace App\Notifications;

use App\Models\ExportTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportTaskCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(ExportTask $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Exportación de Ventas Completada')
            ->greeting('¡Hola '.$notifiable->name.'!')
            ->line('Tu exportación de ventas ha sido completada exitosamente.')
            ->line('Detalles:')
            ->line('- Fecha inicio: '.$this->task->start_date->format('d/m/Y'))
            ->line('- Fecha fin: '.$this->task->end_date->format('d/m/Y'))
            ->line('- Categoría: '.$this->task->category)
            ->line('- Subcategoría: '.$this->task->subcategory)
            ->action('Descargar Archivo', url('/nova/download-export/'.$this->task->id))
            ->line('El archivo estará disponible por 7 días.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'export_completed',
            'task_id' => $this->task->id,
            'message' => 'Exportación completada exitosamente',
        ];
    }
}
