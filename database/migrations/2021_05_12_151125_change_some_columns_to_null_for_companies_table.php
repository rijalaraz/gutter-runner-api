<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSomeColumnsToNullForCompaniesTable extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('couleur_principale')->nullable()->change();
            $table->string('couleur_secondaire')->nullable()->change();
            $table->string('courriel_principal')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {

        });
    }
}
