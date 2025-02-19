<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCompanyIdFromAddressesTable extends Migration
{
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }

    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->integer('company_id')->index()->nullable()->after('country');
        });
    }
}
