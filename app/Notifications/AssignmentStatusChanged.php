<?php

namespace App\Notifications;

use App\Models\BookingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public BookingAssignment $assignment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->assignment->booking;

        return (new MailMessage())
            ->subject('Assignment Status Updated')
            ->line('An assignment has been updated.')
            ->line('Booking: ' . ($booking?->reference_code ?? $this->assignment->booking_id))
            ->line('Status: ' . $this->assignment->status->label());
    }
}
