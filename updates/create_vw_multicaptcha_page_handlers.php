<?php
namespace VijayWilson\Multicaptcha\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateVwMulticaptchaPageHandlersTable extends Migration
{
    public function up()
    {
        Schema::create('vijaywilson_multicaptcha_page_handlers', function($table)
		{
			$table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('page_id');
            $table->string('handler');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('vijaywilson_multicaptcha_page_handlers');
    }
}