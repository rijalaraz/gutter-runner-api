<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceptionDateAndStatutChangeDateToDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->timestamp('reception_date')->index()->nullable()->after('created_by');
            $table->timestamp('statut_change_date')->index()->nullable()->after('statut');
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->dropColumn('statut_change_date');
            $table->dropColumn('reception_date');
        });
    }
}
