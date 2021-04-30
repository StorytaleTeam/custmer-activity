<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Query\Illustration\IllustrationDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class IllustrationController extends AbstractActionController
{
    /** @var IllustrationDataProvider */
    private IllustrationDataProvider $illustrationDataProvider;

    public function __construct(IllustrationDataProvider $illustrationDataProvider)
    {
        $this->illustrationDataProvider = $illustrationDataProvider;
    }

    public function getCustomerActivityAction()
    {
        try {
            $customerId = $this->params()->fromQuery('customerId');
            $illustrationIds = $this->params()->fromQuery('illustrationIds');
            if (empty($customerId)) {
                throw new ValidationException('Need not empty `customerId` param.');
            }
            if (empty($illustrationIds)) {
                throw new ValidationException('Need not empty `illustrationIds` array.');
            }

            $result['activitys'] = $this->illustrationDataProvider->getActivityForCustomer($customerId, $illustrationIds);
            $response = ['success' => true, 'result' =>$result];
        } catch (ValidationException $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        return new JsonModel($response);
    }
}