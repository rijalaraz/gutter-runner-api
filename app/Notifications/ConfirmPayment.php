<?php

namespace App\Notifications;

use Laravel\Cashier\Notifications\ConfirmPayment as CashierConfirmPayment;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmPayment extends CashierConfirmPayment
{

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('cashier.payment', ['id' => $this->paymentId]);

        return (new MailMessage)
            ->subject(__('Confirm Payment'))
            ->greeting(__('Confirm your :amount payment', ['amount' => $this->amount]))
            ->line(__('Extra confirmation is needed to process your payment. Please continue to the payment page by clicking on the button below.'))
            ->action(__('Confirm Payment'), $url);
    }

}