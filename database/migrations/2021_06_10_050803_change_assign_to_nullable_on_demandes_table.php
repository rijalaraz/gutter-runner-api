<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAssignToNullableOnDemandesTable extends Migration
{
    public function up()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->integer('assigned_to')->nullable()->change(); 
        });
    }

    public function down()
    {
        Schema::table('demandes', function (Blueprint $table) {
            $table->integer('assigned_to')->nullable(false)->change(); 
        });
    }
}
