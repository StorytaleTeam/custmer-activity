<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Download\DownloadService;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class DownloadCustomerController extends AbstractRestfulController
{
    /** @var DownloadService */
    private DownloadService $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
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
}