<?php

namespace RestAPI\Controller;

use Storytale\CustomerActivity\Application\Command\Download\DownloadService;
use Zend\Mvc\Controller\AbstractRestfulController;

class DownloadController extends AbstractRestfulController
{
    /** @var DownloadService */
    private DownloadService $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }


}