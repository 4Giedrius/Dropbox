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
        if (!isset($_FILES['file_data'])) {
            throw new \Box_Exception('File was not uploaded. Please contact support.');
        }

        if (!isset($data['rel_id']) && empty($data['rel_id'])) {
            throw new \Box_Exception('File related order ID is missing');
        }

        if (!isset($data['extension']) && empty($data['extension'])) {
            throw new \Box_Exception('Extension name is missing');
        }

        $client_id = $this->getIdentity()->id;

        return $this->getService()->uploadFile($_FILES['file_data'], $client_id, $data['rel_id'], $data['extension']);
    }

    public function get_file($data)
    {
        if (!isset($data['rel_id']) || empty($data['rel_id'])) {
            throw new \Box_Exception('Related  object ID is missing');
        }
        if (!isset($data['extension']) || empty($data['extension'])) {
            throw new \Box_Exception('Extension name is missing');
        }

        $bindings    = array(
            ':rel_id'    => $data['rel_id'],
            ':extension' => $data['extension'],
            ':client_id' => $this->getIdentity()->id,
        );
        $dropboxFile = $this->di['db']->findOne('dropbox', 'rel_id = :rel_id AND client_id = :client_id AND extension LIKE :extension', $bindings);
        if (!$dropboxFile) {
            throw new \Box_Exception('File does not exist');
        }

        return $this->getService()->downloadFile($dropboxFile);
    }
}