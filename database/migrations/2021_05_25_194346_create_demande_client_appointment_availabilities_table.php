<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeClientAppointmentAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_client_appointment_availabilities', function (Blueprint $table) {
            $table->id();
            $table->integer('demande_client_appointment_id')->index('appointment_index');
            $table->integer('demande_client_availability_id')->index('avalaibility_index');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demande_client_appointment_availabilities');
    }
}
