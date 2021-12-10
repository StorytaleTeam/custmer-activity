<?php

namespace Storytale\CustomerActivity\Application\Command\Download;

use Storytale\Contracts\EventBus\EventBus;
use Storytale\Contracts\Persistence\DomainSession;
use Storytale\Contracts\SharedEvents\Illustration\IllustrationWasDownload;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\Query\Illustration\RemoteIllustrationDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\DomainException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\DownloadProcessingService;
use Storytale\CustomerActivity\Domain\PersistModel\Illustration\Illustration;

class DownloadService
{
    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var DomainSession */
    private DomainSession  $domainSession;

    /** @var DownloadProcessingService */
    private DownloadProcessingService $downloadProcessingService;

    /** @var RemoteIllustrationDataProvider */
    private RemoteIllustrationDataProvider $remoteIllustrationDataProvider;

    /** @var EventBus */
    private EventBus $eventBus;

    public function __construct(
        CustomerRepository  $customerRepository,
        DomainSession $domainSession,
        DownloadProcessingService $downloadProcessingService,
        RemoteIllustrationDataProvider $remoteIllustrationDataProvider,
        EventBus $eventBus
    )
    {
        $this->customerRepository = $customerRepository;
        $this->domainSession = $domainSession;
        $this->downloadProcessingService = $downloadProcessingService;
        $this->remoteIllustrationDataProvider = $remoteIllustrationDataProvider;
        $this->eventBus = $eventBus;
    }

    public function downloadIllustration(int $illustrationId, int $customerId): OperationResponse
    {
        $result = null;
        $message = null;
        $code = null;

        try {
            $customer = $this->customerRepository->get($customerId);
            if (!$customer instanceof Customer) {
                throw new ValidationException('Customer with this id not found.');
            }
            $illustration = $this->remoteIllustrationDataProvider->get($illustrationId);
            if (!$illustration instanceof Illustration) {
                throw new ValidationException("Illustration with id $illustrationId not found.");
            }


            try {
                $this->downloadProcessingService->getDownloadPass($customer, $illustration);
            } catch (DomainException $e) {
                throw new ValidationException($e->getMessage(), $e->getCode());
            }

            $illustrationZip = $this->remoteIllustrationDataProvider->getZip($illustration->getId());
            if (!isset($illustrationZip['zip']) || empty($illustrationZip['zip'])) {
                throw new ValidationException('Error occurrence with zip generating.');
            }

            try {
                $isNewDownload = $this->downloadProcessingService->trackDownload($customer, $illustration);
            } catch (DomainException $e) {
                throw new ValidationException($e->getMessage(), $e->getCode());
            }

            $this->domainSession->flush();

            if ($isNewDownload) {
                $this->eventBus->fire(new IllustrationWasDownload($illustrationZip['illustration'] ?? []));
            }
            $result['zip'] = $illustrationZip['zip'];

            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
            $code = $e->getCode();
        }

        return new OperationResponse($success, $result, $message, $code);
    }
}