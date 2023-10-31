<?php namespace Lucid\Employment\Updates;

use Schema;
use Winter\Storm\Database\Updates\Migration;

class BuilderTableUpdateLucidEmploymentJobs extends Migration
{
    public function up()
{
    Schema::table('lucid_employment_jobs', function($table)
    {
        $table->boolean('is_published')->nullable()->unsigned(false)->default(null)->change();
    });
}

public function down()
{
    Schema::table('lucid_employment_jobs', function($table)
    {
        $table->dateTime('is_published')->nullable()->unsigned(false)->default(null)->change();
    });
}
}
