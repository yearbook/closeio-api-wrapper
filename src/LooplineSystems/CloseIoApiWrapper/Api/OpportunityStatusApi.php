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
use LooplineSystems\CloseIoApiWrapper\Model\OpportunityStatus;

class OpportunityStatusApi extends AbstractApi
{
    const NAME = 'OpportunityStatusApi';

    /**
     * {@inheritdoc}
     */
    protected function initUrls()
    {
        $this->urls = [
            'get-statuses' => '/status/opportunity/',
            'add-status' => '/status/opportunity/',
            'get-status' => '/status/opportunity/[:id]/',
            'update-status' => '/status/opportunity/[:id]/',
            'delete-status' => '/status/opportunity/[:id]/'
        ];
    }

    /**
     * @param OpportunityStatus $status
     *
     * @return OpportunityStatus
     *
     * @throws InvalidParamException
     * @throws BadApiRequestException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function addStatus(OpportunityStatus $status)
    {
        $status = json_encode($status);
        $apiRequest = $this->prepareRequest('add-status', $status);
        $response = $this->triggerPost($apiRequest);

        return new OpportunityStatus($response->getData());
    }

    /**
     * @param OpportunityStatus $status
     *
     * @return OpportunityStatus
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function updateStatus(OpportunityStatus $status)
    {
        if ($status->getId() == null) {
            throw new InvalidParamException('When updating a status you must provide the statuses ID');
        }

        $id = $status->getId();
        $status->setId(null);

        $status = json_encode($status);
        $apiRequest = $this->prepareRequest('update-status', $status, ['id' => $id]);
        $response = $this->triggerPut($apiRequest);

        return new OpportunityStatus($response->getData());
    }

    /**
     * @return OpportunityStatus[]
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function getAllStatus()
    {
        $statuses = [];

        $apiRequest = $this->prepareRequest('get-statuses');

        $result = $this->triggerGet($apiRequest);

        if ($result->getReturnCode() == 200) {
            $rawData = $result->getData()[CloseIoResponse::GET_RESPONSE_DATA_KEY];
            foreach ($rawData as $status) {
                $statuses[] = new OpportunityStatus($status);
            }
        }
        return $statuses;
    }

    /**
     * @param string $id
     *
     * @return OpportunityStatus
     *
     * @throws BadApiRequestException
     * @throws InvalidParamException
     * @throws UrlNotSetException
     * @throws ResourceNotFoundException
     */
    public function getStatus($id)
    {
        $apiRequest = $this->prepareRequest('get-status', null, ['id' => $id]);

        /** @var CloseIoResponse $result */
        $result = $this->triggerGet($apiRequest);

        return new OpportunityStatus($result->getData());
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
    public function deleteStatus($id){
        $apiRequest = $this->prepareRequest('delete-status', null, ['id' => $id]);

        $result = $this->triggerDelete($apiRequest);

        return $result->isSuccess();
    }
}
