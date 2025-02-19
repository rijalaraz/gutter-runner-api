<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('passwords.message.subject', [
                'app_name' => config('app.name')
            ]))
            // ->greeting(trans('passwords.message.greeting'))
            ->line(trans('passwords.message.line1'))
            ->action(trans('passwords.message.action.text'), trans('passwords.message.action.url',[
                'app_url'   =>  url(config('app.url')),
                'token'     =>  $this->token,
                'email'     =>  urlencode($notifiable->email),
            ]));
            // ->line('If you did not request a password reset, no further action is required.')
            //->salutation(trans('passwords.message.salutation'));
    }
}
