<?php
/**
 * Close.io Api Wrapper - LLS Internet GmbH - Loopline Systems
 *
 * @link      https://github.com/loopline-systems/closeio-api-wrapper for the canonical source repository
 * @copyright Copyright (c) 2014 LLS Internet GmbH - Loopline Systems (http://www.loopline-systems.com)
 * @license   https://github.com/loopline-systems/closeio-api-wrapper/blob/master/LICENSE (MIT Licence)
 */

namespace LooplineSystems\CloseIoApiWrapper\Api;

use LooplineSystems\CloseIoApiWrapper\CloseIoResponse;
use LooplineSystems\CloseIoApiWrapper\Library\Api\AbstractApi;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\BadApiRequestException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\InvalidParamException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\ResourceNotFoundException;
use LooplineSystems\CloseIoApiWrapper\Library\Exception\UrlNotSetException;
use LooplineSystems\CloseIoApiWrapper\Model\Contact;

class ContactApi extends AbstractApi
{
    const NAME = 'ContactApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-contacts' => '/contact/',
            'add-contact' => '/contact/',
            'get-contact' => '/contact/[:id]/',
            'update-contact' => '/contact/[:id]/',
            'delete-contact' => '/contact/[:id]/',
        ];
    }

    /**
     * @return Contact[]
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function getAllContacts()
    {
        $contacts = [];

        $apiRequest = $this->prepareRequest('get-contacts');

        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200) {
            $rawData = $result->getData()[CloseIoResponse::GET_RESPONSE_DATA_KEY];

            foreach ($rawData as $contact) {
                $contacts[] = new Contact($contact);
            }
        }

        return $contacts;
    }

    /**
     * @param string $id
     *
     * @return Contact
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function getContact($id)
    {
        $apiRequest = $this->prepareRequest('get-contact', null, ['id' => $id]);

        $result = $this->triggerGet($apiRequest);

        return new Contact($result->getData());
    }

    /**
     * @param Contact $contact
     *
     * @return Contact
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function addContact(Contact $contact)
    {
        $contact = json_encode($contact);
        $apiRequest = $this->prepareRequest('add-contact', $contact);

        return new Contact($this->triggerPost($apiRequest)->getData());
    }

    /**
     * @param Contact $contact
     *
     * @return Contact
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function updateContact(Contact $contact)
    {
        // remove id from contact since it won't be part of the patch data
        $id = $contact->getId();
        $contact->setId(null);

        $contact = json_encode($contact);
        $apiRequest = $this->prepareRequest('update-contact', $contact, ['id' => $id]);
        $response = $this->triggerPut($apiRequest);

        return new Contact($response->getData());
    }

    /**
     * @param string $id
     *
     * @return bool
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function deleteContact($id)
    {
        $apiRequest = $this->prepareRequest('delete-contact', null, ['id' => $id]);

        $result = $this->triggerDelete($apiRequest);

        return $result->isSuccess();
    }
}
