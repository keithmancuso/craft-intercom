<?php
namespace Craft;

class IntercomService extends BaseApplicationComponent {


  public function checkAccess() {

    $plugin = craft()->plugins->getPlugin('bugHerd');
    $settings = $plugin->getSettings();

    $showTab = false;

    if ($settings->getAttribute('frontEnd'))
    {

      if ($settings->getAttribute('publicAccess'))
      {
        if (craft()->userSession->isLoggedIn())
        {
          $showTab = true;
        }
      }
      else
      {
        if (craft()->userSession->checkPermission('accessCp'))
        {
          $showTab = true;
        }
      }

    } else {

      if ( craft()->request->isCpRequest() )
      {
        $showTab = true;
      }
    }

    return $showTab;

  }

  public function buildCode()
  {

    $plugin = craft()->plugins->getPlugin('intercom');
    $settings = $plugin->getSettings();

    $user = craft()->userSession->getUser();
    $code = '';





    //user_hash: "'.hash_hmac("sha256", $user->email, "yPV7F8NxueYdjKkVFiDZotFgTTrKTYZSWc_rM2do").'",

    if($user) {

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

        $code = '<script>
        window.intercomSettings = {
          name: "'.$user->fullName.'",
          email: "'.$user->email.'",
          app_id: "'.$settings->app_id.'",
          "admin": "'.$user->admin.'"
          created_at: "'.strtotime($user->dateCreated).'",

          window.intercomSettings = {
            "company": {

              "name": "Intercorp",
              "url":
              // TODO: Insert the current company created at UNIX timestamp here
              "created_at": 1234567890,
              // TODO (optional): Insert the name of the plan the current company is on
              "plan": "pro",
              // TODO (optional): Insert the amount the current company spends a month
              "monthly_spend": 10,
              // TODO (optional): Add any custom attributes, e.g.
              "upgraded_at": 39201029
            },
            "app_id": "aadhbcv9"
          }
        };
        </script>
        <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic(\'reattach_activator\');ic(\'update\',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement(\'script\');s.type=\'text/javascript\';s.async=true;s.src=\'https://widget.intercom.io/widget/aadhbcv9\';var x=d.getElementsByTagName(\'script\')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent(\'onload\',l);}else{w.addEventListener(\'load\',l,false);}}})()</script>';
      }
    }



    return $code;
  }
}
