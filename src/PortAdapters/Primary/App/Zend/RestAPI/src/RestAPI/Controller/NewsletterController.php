<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Newsletter\DTO\NewsletterSubscriptionDTO;
use Storytale\CustomerActivity\Application\Command\Newsletter\NewsletterService;
use Storytale\CustomerActivity\Application\Query\Newsletter\NewsletterSubscriptionDataProvider;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class NewsletterController extends AbstractActionController
{
    /** @var NewsletterService */
    private NewsletterService $newsletterService;

    /** @var NewsletterSubscriptionDataProvider */
    private NewsletterSubscriptionDataProvider $newsletterSubscriptionDataProvider;

    public function __construct(NewsletterService $newsletterService, NewsletterSubscriptionDataProvider $newsletterSubscriptionDataProvider)
    {
        $this->newsletterService = $newsletterService;
        $this->newsletterSubscriptionDataProvider = $newsletterSubscriptionDataProvider;
    }

    public function subscribeAction()
    {
        $params = $this->params()->fromPost(null, []);
        $newsletterSubscriptionDTO = new NewsletterSubscriptionDTO($params);
        $response = $this->newsletterService->subscribe($newsletterSubscriptionDTO);

        return new JsonModel($response->jsonSerialize());
    }

    public function unsubscribeAction()
    {
        $params = $this->params()->fromPost(null, []);
        $newsletterSubscriptionDTO = new NewsletterSubscriptionDTO($params);
        $response = $this->newsletterService->unsubscribe($newsletterSubscriptionDTO);

        return new JsonModel($response->jsonSerialize());
    }

    public function getCountAction()
    {
        $params = $this->params()->fromQuery(null, []);
        $count = $this->newsletterSubscriptionDataProvider->count($params);
        $response = [
            'success' => true,
            'result' => ['count' => $count],
        ];

        return new JsonModel($response);
    }

    public function getListAction()
    {
        $params = $this->params()->fromQuery(null, []);
        $newsSubscriptions = $this->newsletterSubscriptionDataProvider->getList($params);
        $response = [
            'success' => true,
            'result' => ['newsletterSubscription' => $newsSubscriptions],
        ];

        return new JsonModel($response);
    }

    public function getListForCustomerAction()
    {
        $customerId = $this->params()->fromQuery('customerId');
        if ($customerId === null) {
            return new JsonModel(['success' => false, 'message' => 'Need not empty `customerId` param']);
        }
        $customerNewsletterSubscription = $this->newsletterSubscriptionDataProvider->getListForCustomer($customerId);
        $response = [
            'success' => true,
            'result' => ['newsletterSubscription' => $customerNewsletterSubscription],
        ];

        return new JsonModel($response);
    }
}