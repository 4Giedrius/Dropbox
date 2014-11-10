<?php
/**
 * BoxBilling
 *
 * @copyright BoxBilling, Inc (http://www.boxbilling.com)
 * @license   Apache-2.0
 *
 * Copyright BoxBilling, Inc
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

namespace Box\Mod\Dropbox;
require_once BB_PATH_MODS . '/Dropbox/dropbox-sdk/autoload.php';


class Service implements \Box\InjectionAwareInterface
{
    protected $di;

    public function setDi($di)
    {
        $this->di = $di;
    }

    public function getDi()
    {
        return $this->di;
    }

    public function getDropboxAppInfo()
    {
        return \Dropbox\AppInfo::loadFromJsonFile(BB_PATH_MODS . "/Dropbox/config.json");
    }

    public function getAuthLink()
    {
        $appInfo      = $this->getDropboxAppInfo();
        $webAuth      = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
        $authorizeUrl = $webAuth->start();

        return $authorizeUrl;
    }

    public function saveToken($authCode)
    {
        $appInfo = $this->getDropboxAppInfo();
        $webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
        list($accessToken, $dropboxUserId) = $webAuth->finish(trim($authCode));
        $api    = $this->di['api_admin'];
        $config = array(
            'ext'             => 'mod_dropbox',
            'auth_code'       => $authCode,
            'access_token'    => $accessToken,
            'dropbox_user_id' => $dropboxUserId
        );

        return $api->extension_config_save($config);
    }

}