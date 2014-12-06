<?php

namespace Craft;

class IntercomPlugin extends BasePlugin
{

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

	public function getSettingsHtml()
	{
		return craft()->templates->render('intercom/settings', array(
			'settings' => $this->getSettings()
		));
	}

  public function hasCpSection()
  {
    return false;
  }


  public function init()
  {

    $settings = $this->getSettings();
		$user = craft()->userSession->getUser();

		if ($settings->app_id && $user) {

			$monitor = false;

			if ($settings->group_handle) {
				foreach ($user->groups as $group) {
					if ($group->handle == $settings->group_handle) {
						$monitor = true;
					}
				}
			} else {
				$monitor = true;
			}

			 if ($monitor) {

				$oldPath = craft()->path->getTemplatesPath();
				$newPath = craft()->path->getPluginsPath().'intercom/templates';
				craft()->path->setTemplatesPath($newPath);

				$user_hash =  hash_hmac("sha256", $user->email, $settings->secret_key);

				$code = craft()->templates->render('code', array(
					'settings' => $settings,
					'user_hash' => $user_hash
				));

				craft()->templates->includeFootHtml($code);
				craft()->path->setTemplatesPath($oldPath);
			}

		}



  }

}
