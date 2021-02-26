<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Download\DownloadService;
use Storytale\CustomerActivity\Application\Query\Customer\CustomerDataProvider;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class DownloadCustomerController extends AbstractRestfulController
{
    /** @var DownloadService */
    private DownloadService $downloadService;

    /** @var CustomerDataProvider */
    private CustomerDataProvider $customerDataProvider;

    public function __construct(DownloadService $downloadService, CustomerDataProvider $customerDataProvider)
    {
        $this->downloadService = $downloadService;
        $this->customerDataProvider = $customerDataProvider;
    }

    public function get($id)
    {
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty customerId param.']);
        }
        $response = $this->downloadService->downloadIllustration($id, $customerId);

        return new JsonModel($response->jsonSerialize());
    }

    public function getList()
    {
        $customerId = $this->params()->fromQuery('customerId');
        if (empty($customerId)) {
            return new JsonModel(['success' => false, 'message' => 'Need CustomerId param.']);
        }

        $downloads = $this->customerDataProvider->findCustomerDownloads($customerId);
        $response = [
            'success' => true,
            'result' => [
                'downloads' => $downloads,
            ],
        ];

        return new JsonModel($response);
    }

}