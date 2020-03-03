<?php 
namespace VijayWilson\Multicaptcha\Models;

use Model;

class MultiCaptchaPageHandler extends Model
{
    /**
	 * Database table used by the model
	 */
	protected $table = 'vijaywilson_multicaptcha_page_handlers';
	/**
	 * Primary key for the database table used by the model
	 */
	protected $primaryKey = 'id';
	/**
	 * Mass assignment fillable fields
	 */
	protected $fillable = ['page_id', 'handler'];

	public function getTableName()
	{
		return $this->table;
	}
}