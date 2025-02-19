<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandeDemandeProductPresentationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demande_demande_product_presentations', function (Blueprint $table) {
            $table->id();
            $table->integer('demande_id')->index('demande_index');
            $table->integer('demande_product_presentation_id')->index('product_pres_index');
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
        Schema::dropIfExists('demande_demande_product_presentations');
    }
}
