<?php 
namespace VijayWilson\Multicaptcha\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class MultiCaptcha extends Controller
{
	public $implement = ['Backend.Behaviors.FormController'];
    public $formConfig = 'config_form.yaml';
	public $requiredPermissions = [
		'vijaywilson.multicaptcha.set_configuration'
	];

	public function __construct()
	{
		parent::__construct();
		BackendMenu::setContext('VijayWilson.Multicaptcha','multicaptcha','multicaptcha');
	}
}
