<?php

namespace familiar\intercom;

use Craft;
use craft\web\Application;
use craft\base\Plugin;

use yii\base\Event;
use craft\web\View;


class Intercom extends Plugin
{

	public static $plugin;

	public $hasCpSettings = true;
	
	function getName()
	{
		return Craft::t('Intercom');
	}

	function getVersion()
	{
		return '0.1';
	}

	function getDeveloper()
	{
		return 'Familiar';
	}

	function getDeveloperUrl()
	{
		return 'http://familiar.nyc';
	}



	protected function defineSettings()
	{
		return array(
			'app_id' => array(AttributeType::String, 'required' => true),
			'group_handle' => array(AttributeType::String, 'required' => false),
			'secret_key' => array(AttributeType::String, 'required' => true)
		);
	}

	protected function createSettingsModel()
	{
		return new \familiar\intercom\models\Settings();
	}

	/**
	 * @return null|string
	 * @throws \Twig_Error_Loader
	 * @throws \yii\base\Exception
	 */
	protected function settingsHtml()
    {
		$viewService = Craft::$app->getView();

		return $viewService->renderTemplate('intercom/settings', [
			'settings' => $this->getSettings(),
		]);
    }

  public function hasCpSection()
  {
    return false;
  }


  public function init()
  {
	
	parent::init();
	self::$plugin = $this;
	
	Event::on(View::class, View::EVENT_END_BODY, function(Event $event) {

		$settings = $this->getSettings();
		$user = Craft::$app->getUser()->getIdentity();

		if ($settings->app_id && $user) {

			$monitor = false;

			if ($settings->group_handle) {
				if ($user->isInGroup($settings->group_handle)) {
					$monitor = true;
				}
			} else {
				$monitor = true;
			}

			$monitor = true;

			

			 if ($monitor) {

				$oldMode = Craft::$app->view->getTemplateMode();
				Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

				$user_hash =  hash_hmac("sha256", $user->email, $settings->secret_key);
				$version = Craft::$app->getVersion();

				$code = Craft::$app->view->renderTemplate('intercom/code', array(
					'settings' => $settings,
					'user_hash' => $user_hash,
					'version' => $version
				));
				Craft::$app->view->setTemplateMode($oldMode);

				echo $code;
				
			}

		}

	});

  }

}
