<?php 
namespace VijayWilson\Multicaptcha\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateVwCaptchaDetailsTable extends Migration
{
	public function up()
	{
		Schema::create('vijaywilson_captcha_details',function($table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('config_id');
			$table->json('captcha_details')->nullable();
			$table->timestamps();
		});
	}
	
	public function down()
	{		
		Schema::drop('vijaywilson_captcha_details');
	}
}

?>