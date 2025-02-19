<?php

namespace Database\Seeders\Client;

use App\Models\Client\ClientPaymentTerm;
use Illuminate\Database\Seeder;

class ClientPaymentTermSeeder extends Seeder
{
     /**
     * @var array|\Illuminate\Support\Collection
     */
    public $paymentTerms = [
        ['name' => "Paiement d'avance"],
        ['name' => 'Paiement sur réception'],
        ['name' => 'Net 15 jours'],
        ['name' => "Net 30 jours"],
        ['name' => 'Net 60 jours'],
        ['name' => 'Net 90 jours'],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPaymentTerms();
    }

      /**
     * @return $this
     */
    public function createPaymentTerms()
    {
        $this->paymentTerms = collect($this->paymentTerms)->map(function ($paymentTerm) {
            return ClientPaymentTerm::create($paymentTerm);
        });

        return $this;
    }
}
