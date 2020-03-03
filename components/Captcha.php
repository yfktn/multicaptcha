<?php
namespace VijayWilson\Multicaptcha\Components;

use Cms\Classes\ComponentBase;
use VijayWilson\Multicaptcha\Models\MultiCaptcha;
use RainLab\Translate\Classes\Translator;

class Captcha extends ComponentBase
{
	public function componentDetails()
	{
		return[
				'name' 			=> 'Multi-Captcha',
				'description'	=> 'Implements more than one google recaptcha widget on a page'
			  ];
	}

	public function defineProperties()
	{
		return [
			'handlers' => [
				'title'       => 'Handlers',
				'description' => 'Enter the action handlers of the forms associated with the page which has to be protected by reCaptcha',
				'type' 		  => 'stringList',
			]
		];
	}

	public function onRun()
	{
		$multicaptcha = MultiCaptcha::getCaptchaDetails();
		$locale = "en";
		if(class_exists('RainLab\Translate\Classes\Translator'))
		{
			$translator = Translator::instance();
    		$locale = $translator->getLocale();
		}

		$this->page['g_recaptcha_error_style'] 	= $multicaptcha['css_classes'];
		$params	= [ 
			'site-key' => (isset($multicaptcha['site_key'])) ? $multicaptcha['site_key'] : '',
			'locale' => $locale 
		];

		$this->addJs('assets/js/multi_captcha.js', http_build_query($params));
		$this->addJs('assets/js/flash.js');
		$this->addCss('assets/css/flash.css');
	}

	/**
	 * Validate g-recaptcha-response  
	 */
	public static function validateCaptcha($captchaResponse)
	{
		$secretKey = MultiCaptcha::getCaptchaDetails()['secret_key'];

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$headers = ['Accept: application/json'];
		$data = ['secret' => $secretKey, 'response' => $captchaResponse];
		$queryString = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result, true);

		return $result['success'];
	}
}

?>