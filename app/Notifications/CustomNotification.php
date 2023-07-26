<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CustomNotification extends Notification
{
    use Queueable;

    public $title;
    public $body;
    public $icon;
    public $description;

    public function __construct($title, $body, $description)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting('skip default')
            ->line('Hello '.$notifiable->name.',')
            ->line(new HtmlString($this->body));
    }
}
