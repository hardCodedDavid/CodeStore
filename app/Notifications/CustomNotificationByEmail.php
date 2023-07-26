<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CustomNotificationByEmail extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $buttonText;
    public $url;

    public function __construct($title, $message, $buttonText = null, $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->buttonText = $buttonText;
        $this->url = $url;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->url){
            return (new MailMessage)
                ->subject($this->title)
                ->greeting('skip default')
                ->line('Hello '.$notifiable->name.',')
                ->line(new HtmlString($this->message))
                ->action($this->buttonText, $this->url);
        }else{
            return (new MailMessage)
                ->subject($this->title)
                ->greeting('skip default')
                ->line('Hello '.$notifiable->name.',')
                ->line(new HtmlString($this->message));
        }
    }
}
