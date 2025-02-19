<?php

use App\Models\Client\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatutAndDateDerniereActiviteToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('active');
            $table->enum('statut', Client::STATUTES)->index()->default(Client::STATUT_INACTIF)->after('note_interne');
            $table->timestamp('date_derniere_activite')->nullable()->after('statut');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('date_derniere_activite');
            $table->dropColumn('statut');
            $table->boolean('active')->default(false);
        });
    }
}
