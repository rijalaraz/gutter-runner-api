<?php

namespace App\Notifications;

use App\Models\Demande\Demande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeCreated extends Notification
{
    use Queueable;

    private $demande;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Demande $demande)
    {
        $this->demande  = $demande;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = new MailMessage;

        $company = $this->demande->creator()->first()->company()->first();

        $message
            ->success()
            ->subject(trans('demande.message.subject', [
                'company_name' => $company->nom,
                'uuid' => $this->demande->uuid,
                'postal_service_address' => $this->demande->service_address()->pluck('postal_address')->toArray()[0],
            ]));

        $message->markdown('mail.demande.created', [
            'client' => $notifiable,
            'demande' => $this->demande,
            'assignee' => $this->demande->assigne()->first(['firstname', 'lastname']),
            'videos' => $this->demande->product_presentations()->get(),
            'company' => $company,
        ]);

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
