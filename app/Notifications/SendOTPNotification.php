<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendOTPNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pantau KTR - Reset password')
            ->greeting('Halo,' . ' ' . $this->details['email'])
            ->line('Anda menerima email ini karena terdapat permintaan untuk mereset password akun Pantau KTR anda')
            ->line('Silahkan masukkan kode dibawah ini untuk mereset password anda')
            ->line($this->details['otp'])
            ->line('Kode diatas hanya berlaku selama' . ' ' . $this->details['expiry'] . ' ' . 'menit')
            ->line('Jika anda tidak merasa melakukan permintaan ubah password silahkan abaikan email ini dan segera ubah password anda dari aplikasi demi keamanan akun anda')
            ->line('Terima kasih telah menggunakan aplikasi Pantau KTR dan ikut berperan dalam memantau Kawasan Tanpa Rokok');
    }
    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
