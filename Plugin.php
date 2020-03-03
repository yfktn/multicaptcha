<?php

namespace VijayWilson\Multicaptcha;

use System\Classes\PluginBase;
use Backend;
use Cms\Classes\CmsController;
use Cms\Classes\Page;
use VijayWilson\Multicaptcha\Models\MultiCaptchaPageHandler;
use DB;

class Plugin extends PluginBase
{
	public function pluginDetails()
	{
		return [
			'name' => 'MultiCaptcha',
			'description' => 'Provides google reCaptcha v2.0 for authentication',
			'author' => 'Vijay Wilson'
		];
	}

	public function registerNavigation()
	{
		return [
			'multicaptcha' => [
				'label' => 'MultiCaptcha',
				'url' => Backend::url('vijaywilson/multicaptcha/multicaptcha/update/captcha'),
				'icon' => 'icon-shield',
				'iconSvg' => '/plugins/vijaywilson/multicaptcha/assets/images/multi-captcha.svg',
				'permissions' => ['vijaywilson.multicaptcha.set_configuration']
			]
		];
	}

	public function registerComponents()
	{
		return [
			'\VijayWilson\Multicaptcha\Components\Captcha' => 'reCaptcha'
		];
	}

	/**
	 * Extended Backend CMS page class to get page information along with the action handlers from the
	 * reCaptcha component.
	 */
	public function register()
	{
		$currentObject = $this;

		Page::extend(function ($model) use ($currentObject) {

			$model->bindEvent('model.afterSave', function () use ($model, $currentObject) {

				$modelId = $model->getIdAttribute();

				$page = Page::find($modelId);
				$currentObject->removePageHandlers($modelId);

				if ($page != null && $page->hasComponent('reCaptcha')) {
					$reCaptchaProperties = $page->getComponentProperties("reCaptcha");

					if (array_key_exists('handlers', $reCaptchaProperties)) {
						$handlers = $reCaptchaProperties['handlers'];
						$currentObject->setreCaptchaPageHandlers($modelId, $handlers);
					}
				}
			});

			$model->bindEvent('model.beforeDelete', function () use ($model, $currentObject) {

				$page = Page::find($model->id);
				if ($page != null && $page->hasComponent('reCaptcha')) {
					$currentObject->removePageHandlers($model->id);
				}
			});
		});
	}

	/**
	 * Register the captchaMiddleware to handle the g-recaptcha-response before a request is routed
	 */
	public function boot()
	{
		CmsController::extend(function ($controller) {
			$controller->middleware('VijayWilson\Multicaptcha\Middleware\captchaMiddleware');
		});
	}

	/**
	 * Remove page handlers from db
	 */
	private function removePageHandlers($pageId)
	{
		$multiCaptchaPageHandler = new MultiCaptchaPageHandler();
		$tableName = $multiCaptchaPageHandler->getTableName();

		DB::table($tableName)->where('page_id', $pageId)->delete();
	}

	/**
	 * Save page id and action handlers in db
	 */
	private function setreCaptchaPageHandlers($pageId, $handlers)
	{
		if (count($handlers) > 0) {
			foreach ($handlers as $handler) {
				MultiCaptchaPageHandler::create(['page_id' => $pageId, 'handler' => $handler]);
			}
		}
	}

	public function registerPermissions()
	{
		return [
			'vijaywilson.multicaptcha.set_configuration' => [
				'label' => 'Manage The Configuration',
				'tab'   => 'MultiCaptcha',
			]
		];
	}
}
