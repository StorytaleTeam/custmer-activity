<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Illustration\Querying\Storytale;

use Storytale\Contracts\ServiceClient\InventoryClient;
use Storytale\CustomerActivity\Application\Query\Illustration\RemoteIllustrationDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\CustomerActivity\Domain\PersistModel\Illustration\Illustration;
use Storytale\CustomerActivity\Domain\PersistModel\Illustration\IllustrationFactory;
use Storytale\PortAdapters\Secondary\ServiceClient\CommandValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\Inventory\Command\DownloadIllustrationCommand;
use Storytale\PortAdapters\Secondary\ServiceClient\Inventory\Command\GetOneIllustrationForCustomerCommand;

class StorytaleRemoteIllustrationDataProvider implements RemoteIllustrationDataProvider
{
    /** @var InventoryClient */
    private InventoryClient $inventoryClient;

    /** @var IllustrationFactory */
    private IllustrationFactory $illustrationFactory;

    public function __construct(InventoryClient $inventoryClient, IllustrationFactory $illustrationFactory)
    {
        $this->inventoryClient = $inventoryClient;
        $this->illustrationFactory = $illustrationFactory;
    }

    public function getZip(int $illustrationId): ?array
    {
        try {
            $response = null;
            $downloadIllustrationCommand = new DownloadIllustrationCommand($this->inventoryClient, ['illustrationId' => $illustrationId]);
            $remoteResponse = $downloadIllustrationCommand->run();
            if (isset($remoteResponse['success']) && $remoteResponse['success'] === true) {
                if (isset($remoteResponse['result']['zip']) && !empty($remoteResponse['result']['zip'])) {
                    $response = $remoteResponse['result'];
                }
            } else {
                $message = $remoteResponse['message'] ?? 'Unexpected response format from remote service';
                throw new ValidationException($message);
            }
        } catch (CommandValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $response;
    }

    public function get(int $illustrationId): ?Illustration
    {
        try {
            $response = null;
            $getOneIllustrationCommand = new GetOneIllustrationForCustomerCommand($this->inventoryClient, ['id' => $illustrationId]);
            $remoteResponse = $getOneIllustrationCommand->run();
            if (isset($remoteResponse['success']) && $remoteResponse['success'] === true) {
                if (isset($remoteResponse['result']['illustrationData']['id']) && !empty($remoteResponse['result']['illustrationData']['id'])) {
                    $response = $this->illustrationFactory->buildFromInventoryResponse($remoteResponse['result']['illustrationData']);
                }
            } else {
                $message = $remoteResponse['message'] ?? 'Unexpected response format from remote service';
                throw new ValidationException($message);
            }
        } catch (CommandValidationException $e) {
            throw new ValidationException($e->getMessage());
        }

        return $response;
    }
}