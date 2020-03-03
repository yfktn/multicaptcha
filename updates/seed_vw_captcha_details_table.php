<?php 
namespace VijayWilson\Multicaptcha\Updates;

use Seeder;
use Db;

class SeedVwCaptchaDetailsTable extends Seeder
{
	public function run()
	{
		Db::table('vijaywilson_captcha_details')->insert(['config_id'=>'captcha']);
	}
}

?>