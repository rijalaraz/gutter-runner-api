<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveClientCompanyTypeIdFromClientCompaniesTable extends Migration
{
    public function up()
    {
        Schema::table('client_companies', function (Blueprint $table) {
            $table->dropColumn('client_company_type_id');
        });
    }

    public function down()
    {
        Schema::table('client_companies', function (Blueprint $table) {
            $table->integer('client_company_type_id')->index()->nullable()->after('name');
        });
    }
}
