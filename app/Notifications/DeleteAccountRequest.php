<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteAccountRequest extends Notification
{
    use Queueable;

    protected $user;
    
    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user  = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting(__('msg.Hi').'!')
                    ->line($this->user->fname.' '.$this->user->lname.' '.trans('msg.email.account-delete'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id'   => $this->user->id,
            'name'      => $this->user->fname.' '.$this->user->lname,
            'email'     => $this->user->email,
            'title'     => trans('msg.notification.delete-title'),
            'msg'       => $this->user->fname.' '.$this->user->lname.' '.trans('notification.email.account-delete'),
            'datetime'  => date('Y-m-d h:i:s'),
        ];
    }
}
