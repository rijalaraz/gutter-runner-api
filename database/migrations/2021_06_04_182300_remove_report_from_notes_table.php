<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveReportFromNotesTable extends Migration
{
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('report');
            $table->boolean('report_to_soumission')->default(true)->after('note');
            $table->boolean('report_to_contrat')->default(true)->after('report_to_soumission');
            $table->boolean('report_to_facture')->default(true)->after('report_to_contrat');
            $table->boolean('report_to_bon_de_travail')->default(true)->after('report_to_facture');
        });
    }

    public function down()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('report_to_bon_de_travail');
            $table->dropColumn('report_to_facture');
            $table->dropColumn('report_to_contrat');
            $table->dropColumn('report_to_soumission');
            $table->boolean('report')->default(true);;
        });
    }
}
