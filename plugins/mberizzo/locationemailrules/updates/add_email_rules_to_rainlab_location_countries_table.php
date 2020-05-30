<?php namespace Mberizzo\LocationEmailRules\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class AddEmailRulesToRainlabLocationCountriesTable extends Migration
{
    public function up()
    {
        Schema::table('rainlab_location_countries', function (Blueprint $table) {
            $table->text('email_rules')->nullable();
        });
    }

    public function down()
    {
        Schema::table('rainlab_location_countries', function (Blueprint $table) {
            $table->dropColumn('email_rules');
        });
    }
}
