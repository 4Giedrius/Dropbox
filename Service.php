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

use RedBeanPHP\SimpleModel;

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

    public function install()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS `dropbox` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `client_id` bigint(20) DEFAULT NULL,
        `rel_id` bigint(20) DEFAULT NULL,
        `extension` varchar(255) DEFAULT NULL,
        `path` varchar(255) DEFAULT NULL,
        `name` varchar(255) DEFAULT NULL,
        `created_at` varchar(35) DEFAULT NULL,
        `updated_at` varchar(35) DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $this->di['db']->exec($sql);
    }

    public function uninstall()
    {
        $this->di['db']->exec('DROP TABLE dropbox;');
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

    public function getDropboxClient()
    {
        $api    = $this->di['api_admin'];
        $config = $api->extension_config_get(array('ext' => 'mod_dropbox'));
        if (!isset($config['access_token']) || empty($config['access_token'])) {
            throw new \Box_Exception('Dropbox access token missing. Please configure it from admin area');
        }
        $accessToken = $config['access_token'];

        return new \Dropbox\Client($accessToken, "PHP-Example/1.0");;

    }

    public function uploadFile($file, $client_id, $rel_id, $extension)
    {
        $f         = fopen($file['tmp_name'], "rb");
        $filename  = $file['name'];
        $dbxClient = $this->getDropboxClient();
        $result    = $dbxClient->uploadFile('/' . $filename, \Dropbox\WriteMode::add(), $f);

        if (isset($result['path']) && !empty($result['path'])) {
            $dropbox             = $this->di['db']->dispense('dropbox');
            $dropbox->client_id  = $client_id;
            $dropbox->rel_id     = $rel_id;
            $dropbox->extension  = $extension;
            $dropbox->path       = $result['path'];
            $dropbox->name       = $filename;
            $dropbox->updated_at = date('c');
            $dropbox->created_at = date('c');
            $this->di['db']->store($dropbox);
        }

        return true;
    }

    public function downloadFile(\RedBeanPHP\OODBBean $dropboxFile)
    {
        $path = $dropboxFile->name;
        $f            = fopen($path, "w+b");
        $dbxClient    = $this->getDropboxClient();
        $fileMetadata = $dbxClient->getFile($dropboxFile->path, $f);
        fclose($f);

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$path" . ";");
        header("Content-Transfer-Encoding: binary");
        readfile($path);
        flush();
        unlink($path);

        return true;
    }
}