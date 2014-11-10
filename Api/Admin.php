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
    public function get_file($data)
    {
        if (!isset($data['rel_id']) || empty($data['rel_id'])) {
            throw new \Box_Exception('Related  object ID is missing');
        }
        if (!isset($data['extension']) || empty($data['extension'])) {
            throw new \Box_Exception('Extension name is missing');
        }

        $bindings = array(
            ':rel_id'    => $data['rel_id'],
            ':extension' => $data['extension'],
        );

        $dropboxFile = $this->di['db']->findOne('dropbox', 'rel_id = :rel_id AND extension LIKE :extension', $bindings);

        if (!$dropboxFile) {
            throw new \Box_Exception('File does not exist');
        }

        return $this->getService()->downloadFile($dropboxFile);

    }

}