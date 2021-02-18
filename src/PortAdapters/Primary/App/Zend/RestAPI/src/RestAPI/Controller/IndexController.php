<?php

namespace RestAPI\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new JsonModel(['success' => true, 'result' => 'skeleton-dev service']);
    }
}