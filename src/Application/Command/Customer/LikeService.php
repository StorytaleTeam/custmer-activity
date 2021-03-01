<?php

namespace Storytale\CustomerActivity\Application\Command\Customer;

use Storytale\Contracts\Persistence\DomainSession;
use Storytale\CustomerActivity\Application\OperationResponse;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\Customer;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerLike;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerLikeFactory;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\CustomerRepository;
use Storytale\CustomerActivity\Domain\PersistModel\Customer\LikeRepository;

class LikeService
{
    /** @var LikeRepository */
    private LikeRepository $likeRepository;

    /** @var DomainSession */
    private DomainSession $domainSession;

    /** @var CustomerRepository */
    private CustomerRepository $customerRepository;

    /** @var CustomerLikeFactory */
    private CustomerLikeFactory $likeFactory;

    public function __construct(
        LikeRepository $likeRepository, DomainSession $domainSession,
        CustomerRepository $customerRepository, CustomerLikeFactory $customerLikeFactory
    )
    {
        $this->likeRepository = $likeRepository;
        $this->domainSession = $domainSession;
        $this->customerRepository = $customerRepository;
        $this->likeFactory = $customerLikeFactory;
    }

    public function likeAction(int $customerId, int $illustrationId): OperationResponse
    {
        $result = null;
        $message = null;

        try {
            $action = null;
            $like = $this->likeRepository->getByCustomerAndIllustration($customerId, $illustrationId);
            if ($like instanceof CustomerLike) {
                $action = 'unlike';
                $this->likeRepository->delete($like);
            } else {
                $customer = $this->customerRepository->get($customerId);
                $newLike = $this->likeFactory->create($customer, $illustrationId);
                if (!$customer instanceof Customer) {
                    throw new ValidationException('User with this id not found.');
                }
                $customer->like($newLike);
                $action = 'like';
            }
            $this->domainSession->flush();

            $result['action'] = $action;
            $success = true;
        } catch (ValidationException $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return new OperationResponse($success, $result, $message);
    }
}