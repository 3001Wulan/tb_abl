<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Seminar;

class SeminarScheduled extends Notification
{
    use Queueable;

    protected $seminar;
    protected $message;

    public function __construct(Seminar $seminar, $message = null)
    {
        $this->seminar = $seminar;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail','database'];
    }

    public function toMail($notifiable)
    {
        $time = $this->seminar->scheduled_at ? $this->seminar->scheduled_at->format('d M Y H:i') : 'Belum dijadwalkan';

        return (new MailMessage)
                    ->subject("Jadwal Seminar KP: {$this->seminar->title}")
                    ->greeting('Assalamualaikum / Halo,')
                    ->line("Topik: {$this->seminar->title}")
                    ->line("Mahasiswa: {$this->seminar->student->name}")
                    ->line("Jadwal: {$time}")
                    ->line($this->message ?? 'Silakan cek detail pada sistem.')
                    ->action('Lihat Seminar', url("/seminars/{$this->seminar->id}"))
                    ->line('Terima kasih.');
    }

    public function toArray($notifiable)
    {
        return [
            'seminar_id' => $this->seminar->id,
            'title' => $this->seminar->title,
            'scheduled_at' => $this->seminar->scheduled_at,
            'student' => $this->seminar->student->only('id','name'),
        ];
    }

    public function toDatabase($notifiable)
{
    return [
        'seminar_id' => $this->seminar->id,
        'title' => $this->seminar->title,
        'scheduled_at' => $this->seminar->scheduled_at,
        'student' => [
            'id' => $this->seminar->student->id,
            'name' => $this->seminar->student->name,
        ],
    ];
}

}
