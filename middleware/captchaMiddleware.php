<?php
namespace VijayWilson\Multicaptcha\Middleware;

use Closure;
use Redirect;
use October\Rain\Exception\AjaxException;
use VijayWilson\Multicaptcha\Components\Captcha;
use VijayWilson\Multicaptcha\Models\MultiCaptcha;
use VijayWilson\Multicaptcha\Models\MultiCaptchaPageHandler;
use Flash;
use Cms\Classes\Theme;
use Cms\Classes\Router;
use RainLab\Translate\Classes\Translator;

class captchaMiddleware
{
	
	private $exception = [
		'input_request'	=> 'g-recaptcha-response',
		'message'		=> ''
	];

	private $currentRequest;

	public function handle($request, Closure $next)
	{
		$this->currentRequest = $request;
		if($this->isRequestProtectedByRecaptcha())
		{
			$isValid = true;

			$multicaptcha = MultiCaptcha::find('captcha');
			
			$this->exception['message'] = $multicaptcha->error_message_select_captcha;

			if(!$this->currentRequest->exists('g-recaptcha-response'))
			{
				$isValid = false;
			}
			else if($this->currentRequest->input('g-recaptcha-response') == '')
			{
				$isValid = false;
			}
			else
			{
				$isValid = Captcha::validateCaptcha($this->currentRequest->input('g-recaptcha-response'));
				$this->exception['message'] = $multicaptcha->error_message_invalid_captcha;
			}

			if(!$isValid)
			{
				return $this->throwErrorResponse();
			}
		}

		return $next($request);
	}

	/**
	 * Get the page url and request handler and pass it for reCaptcha protection verification 
	 */
	private function isRequestProtectedByRecaptcha()
	{
		$isRequestProtectedByRecaptcha = false;

		if($this->verifyRequestHandler())
		{
			$isRequestProtectedByRecaptcha = true;
		}

		return $isRequestProtectedByRecaptcha;
	}

	/**
	 * Verify whether the request handler for the page exists in db
	 */
	private function verifyRequestHandler()
	{
		$isVerified = false;

		$requestHandler = $this->getRequestHandler();
		if($requestHandler != null)
		{
			$page = $this->getPage();
			if($page != null)
			{
				$multicaptchaPageHandler = MultiCaptchaPageHandler::where('page_id', $page->id)->where('handler', $requestHandler)->first();
				if($multicaptchaPageHandler != null)
				{
					$isVerified = true;
				}
			}
		}
		return $isVerified;
	}

	/**
	 * Get page by url path
	 */
	private function getPage()
	{
		$page = null;
		$theme = Theme::getActiveTheme();
		if($theme != null)
		{
			$router = new Router($theme);
			$url = $this->getUrlPath();
			$page = $router->findByUrl($url);
		}
		return $page;
	}

	/**
	 * Get url path from request
	 */
	private function getUrlPath()
	{
		$urlPath = $this->currentRequest->path();
		if(class_exists('RainLab\Translate\Classes\Translator'))
		{
			$translator = Translator::instance();
			if ($translator->isConfigured() && $translator->loadLocaleFromRequest() && ($locale = $translator->getLocale())) 
			{
				$segments = explode("/", $urlPath);
				if(count($segments) > 0)
				{
					if($segments[0] == $locale)
					{
						array_shift($segments);
						$urlPath = implode('/', $segments);
					}
				}
			}
		}
		return $urlPath;
	}

	/**
	 * Get the action handler from the request 
	 */
	private function getRequestHandler()
	{
		$handler = null;
		if($this->currentRequest->ajax())
		{
			$handler = $this->currentRequest->header('X-OCTOBER-REQUEST-HANDLER');
		}
		else
		{
			$handler = $this->currentRequest->input('_handler');
		}

		return $handler;
	}

	/**
	 * Return error response based on the request type    
	 */
	private function throwErrorResponse()
	{
		if($this->currentRequest->ajax())
		{
			throw new ajaxException(json_encode($this->exception));
		}

		Flash::error(json_encode($this->exception));
		return Redirect::back();
	}
}

?>