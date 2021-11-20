<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use NotificationChannels\Fcm\Resources\Notification as ResourcesNotification;

class LaporanProcessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setNotification(
                ResourcesNotification::create()
                    ->setTitle('Laporan' . ' ' . $this->details['nomor_laporan'])
                    ->setBody('Telah' . ' ' . $this->details['status'] . ' ' . $this->details['keterangan'] . ' ' . 'Terus Pantau KTR di sekitar anda.')
            )
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()
                        ->setAnalyticsLabel('analytics'))
            )
            ->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()
                        ->setAnalyticsLabel('analytics_ios'))
                    ->setHeaders([
                        'apns-priority' => 5
                    ])
                    ->setPayload([
                        'aps' => [
                            'sound' => "default"
                        ]
                    ])
            );
    }
    
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
