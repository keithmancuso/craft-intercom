<?php
namespace familiar\intercom\models;

use craft\base\Model;
use craft\validators\StringValidator;

/**
 * Class Settings
 * @package benf\embeddedassets\models
 */
class Settings extends Model
{
	/**
	 * @var string
	 */
	public $app_id = '';

	/**
	 * @var string
	 */
	public $group_handle = '';

	/**
	 * @var string
	 */
	public $secret_key = '';


	/**
	 * @return array
	 */
	public function rules()
	{
		return [
            ['app_id', 'required'],
            ['group_handle', 'string'],
            ['secret_key', 'required']
        ];
	}
}
