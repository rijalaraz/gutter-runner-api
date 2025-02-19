<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeClientIdToStringOnDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->string('client_id')->index()->change()->after('confirmation_email');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->integer('client_id')->index()->change()->after('confirmation_email');
        });
    }
}
