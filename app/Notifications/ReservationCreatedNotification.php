<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCreatedNotification extends Notification
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
            'message'        => "Your reservation has been created successfully.",
        ];
    }
}
