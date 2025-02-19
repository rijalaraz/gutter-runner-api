<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCompanyIdFromPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->integer('company_id')->index()->nullable()->after('url');
        });
    }
}
