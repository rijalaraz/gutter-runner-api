<?php

use App\Models\Demande\Demande;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index()->unique();
            $table->string('numero');
            $table->string('created_by');
            $table->enum('statut', Demande::STATUTES)->default(Demande::STATUT_BROUILLON);
            $table->string('delai_de_reponse')->nullable();
            $table->integer('assigned_to'); // user_id
            $table->boolean('urgent')->default(false);
            $table->integer('demande_source_id');
            $table->text('plus_de_details');
            $table->boolean('confirmation_email')->default(false);
            $table->integer('client_id');
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
        Schema::dropIfExists('demandes');
    }
}
