<?php
namespace VijayWilson\Multicaptcha\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class RemoveVwMulticaptchaPageUrlsAndHandlersTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('vijaywilson_multicaptcha_page_url_handlers');
        Schema::dropIfExists('vijaywilson_multicaptcha_page_urls');
    }

    public function down()
    {
        Schema::dropIfExists('vijaywilson_multicaptcha_page_url_handlers');
        Schema::dropIfExists('vijaywilson_multicaptcha_page_urls');
    }
}