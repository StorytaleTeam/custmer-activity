<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Newsletter\DTO\NewsletterSubscriptionDTO;
use Storytale\CustomerActivity\Application\Command\Newsletter\NewsletterService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class NewsletterController extends AbstractActionController
{
    /** @var NewsletterService */
    private NewsletterService $newsletterService;

    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
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
}