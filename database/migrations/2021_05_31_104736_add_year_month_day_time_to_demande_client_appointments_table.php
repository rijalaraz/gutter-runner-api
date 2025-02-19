<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearMonthDayTimeToDemandeClientAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('demande_client_appointments', function (Blueprint $table) {
            $table->year('year')->index()->after('appointment_date');
            $table->string('month',2)->index()->after('year');
            $table->string('day',2)->index()->after('month');
            $table->time('time')->index()->after('day');
        });
    }

    public function down()
    {
        Schema::table('demande_client_appointments', function (Blueprint $table) {
            $table->dropColumn('time');
            $table->dropColumn('day');
            $table->dropColumn('month');
            $table->dropColumn('year');
        });
    }
}
