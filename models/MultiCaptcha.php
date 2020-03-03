<?php 
namespace VijayWilson\Multicaptcha\Models;

use Model;
/**
 * MultiCaptcha Model
 */
class MultiCaptcha extends Model
{
	/**
	 * Database table used by the model
	 */
	protected $table = 'vijaywilson_captcha_details';
	/**
	 * Primary key for the database table used by the model
	 */
	protected $primaryKey = 'config_id';
	/**
	 * Jsonable field to store array of purgeable fields with values
	 */
	protected $jsonable = ['captcha_details'];
	
	use \October\Rain\Database\Traits\Validation;
	
	public $rules = [
						'site_key' 						=> 'required',
						'secret_key'					=> 'required',
						'error_message_select_captcha'	=> 'required',
						'error_message_invalid_captcha'	=> 'required',
						'domain_address'=> 'url'
				    ];
					
	/*
	 * Custom error message for domain address
	 */
	public $customMessages = ['url' => 'The :attribute should be a valid url ie., http(s)://www.example.com'];

	/*
	 * Custom attribute name
	 */
	public $attributeNames = [
								'error_message_select_captcha'	=> 'Error message for unselected captcha',
								'error_message_invalid_captcha'	=> 'Error message for invalid captcha'
							 ];

	/*
	 * Check if Translatable Model exists or not, before routing the request
	 */
	public static function boot()
	{
		parent::boot();

		if(class_exists('RainLab\Translate\Behaviors\TranslatableModel'))
		{
			self::extend(function($model)
			{
				$model->implement[] = 'RainLab.Translate.Behaviors.TranslatableModel';
			});
		}
	}

    public $translatable = [
                             'error_message_select_captcha',
							 'error_message_invalid_captcha'
                           ];


	use \October\Rain\Database\Traits\Purgeable;
	
	/**
	 * array of Purgeable fields 
	 */
	protected $purgeable = [
								'site_key',
								'secret_key',
								'label_name',
								'domain_address',
								'error_message_select_captcha',
								'error_message_invalid_captcha',
								'css_classes',
							];
							
	public $fieldValues = [];
	
	public function afterFetch()
	{
		$this->fieldValues = ($this->captcha_details)?$this->captcha_details:[];
		$this->attributes = array_merge($this->fieldValues,$this->attributes);
	}

	public function beforeSave()
	{
		$fields = array_values($this->purgeable);
		foreach($fields as $field)
		{
			$this->fieldValues[$field] = $this->getOriginalPurgeValue($field);
		}
		$this->captcha_details = $this->fieldValues;
	}
	/**
	 * Return the captcha_details
	 */
	public static function getCaptchaDetails()
	{
		$model = MultiCaptcha::where('config_id','captcha')->first();
		return $model->captcha_details;
	}
}

?>