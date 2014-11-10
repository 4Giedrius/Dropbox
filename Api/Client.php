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

class Client extends \Api_Abstract
{
    public function upload_file($data)
    {
        if (!isset($_FILES['file_data'])){
            throw new \Box_Exception('File was not uploaded. Please contact support.');
        }

        return $this->getService()->uploadFile($_FILES['file_data']);
    }
}