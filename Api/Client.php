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
    /**
     * Upload file to Dropbox. Uses $_FILES array so make sure your form is
     * enctype="multipart/form-data"
     *
     * @param int $rel_id - rel_id id
     * @param string $extension - rel_id id
     * @param file $file_data - <input type="file" name="file_data" /> field content
     *
     * @return bool
     * @throws \Box_Exception
     */
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

    /**
     * Downloads file attached to any extension
     *
     * @param int $rel_id - related object ID
     * @param string $extension - extension title
     * @return bool
     * @throws \Box_Exception
     */
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

    /**
     * Checks if the particular extension object has attachment
     *
     * @param int $rel_id - related object ID
     * @param string $extension - extension title
     * @return bool
     * @throws \Box_Exception
     */
    public function has_upload($data)
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
            return false;
        }

        return true;
    }
}