<?php

namespace App\Notifications;

use App\Models\ExportTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportTaskFailed extends Notification implements ShouldQueue
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
            ->subject('Error en Exportación de Ventas')
            ->greeting('Hola '.$notifiable->name)
            ->line('Ha ocurrido un error al procesar tu exportación de ventas.')
            ->line('Error: '.$this->task->error_message)
            ->line('Por favor, intenta nuevamente o contacta a soporte si el problema persiste.')
            ->action('Ir a Nova', url('/nova'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'export_failed',
            'task_id' => $this->task->id,
            'message' => 'Error en la exportación: '.$this->task->error_message,
        ];
    }
}
