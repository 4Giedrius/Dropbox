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

/**
 *DropBox file upload
 */
namespace Box\Mod\Dropbox\Api;

class Admin extends \Api_Abstract
{
    public function save_token($data)
    {
        if (!isset($data['auth_code']) || empty($data['auth_code'])){
            throw new \Box_Exception('Dropbox authentication code missing');
        }
        return $this->getService()->saveToken($data['auth_code']);
    }

}