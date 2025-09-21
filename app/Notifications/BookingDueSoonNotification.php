<?php

namespace App\Notifications;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingDueSoonNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Booking Requires Attention')
            ->line("Booking {$this->booking->reference_code} is due soon with progress {$this->booking->progress_percent}%.")
            ->action('View Booking', BookingResource::getUrl('edit', ['record' => $this->booking]));
    }
}
