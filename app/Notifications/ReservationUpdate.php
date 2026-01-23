<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\BroadcastMessage;
class ReservationUpdate extends Notification
{
    use Queueable;

    protected $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function via(object $notifiable): array
    {
        return ['database','broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'reservation_id' => $this->reservation->id,
            'flat_id' => $this->reservation->flat_id,
            'message' => 'Reservation updated successfully',
        ]);
    }

    public function broadcastOn()
    {
        return new \Illuminate\Broadcasting\PrivateChannel('Booking_notification');
    }

    public function broadcastAs()
    {
        return 'reservation.updated';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id'=>$this->reservation->id,
            'flat_id'=>$this->reservation->flat_id,
            'message'=>'Your reservation has been updated successfully',
        ];
    }
}
