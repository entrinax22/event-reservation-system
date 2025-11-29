<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUpdateReservationNotification extends Notification
{
    use Queueable;

    private $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function via($notifiable)
    {
        return ['database'];  
    }

    public function toDatabase($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->reserved_event_id,
            'event_date'     => $this->reservation->event_date,
            'status'         => $this->reservation->status,
            'message'        => "Your reservation has been updated.",
        ];
    }
}
