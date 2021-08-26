<?php

namespace RestAPI\Controller;

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

    public function subscribeEmailAction()
    {
        $email = $this->params()->fromPost('email');
        $response = $this->newsletterService->subscribeEmail($email);

        return new JsonModel($response->jsonSerialize());
    }

    public function unsubscribeUuidAction()
    {
        $uuid = $this->params()->fromPost('uuid');
        $response = $this->newsletterService->unsubscribeByUuid($uuid);

        return new JsonModel($response->jsonSerialize());
    }
}