<?php

namespace Storytale\CustomerActivity\PortAdapters\Secondary\Illustration\Querying\Storytale;

use Storytale\Contracts\ServiceClient\InventoryClient;
use Storytale\CustomerActivity\Application\Query\Illustration\RemoteIllustrationDataProvider;
use Storytale\CustomerActivity\Application\ValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\CommandValidationException;
use Storytale\PortAdapters\Secondary\ServiceClient\Inventory\Command\DownloadIllustrationCommand;

class StorytaleRemoteIllustrationDataProvider implements RemoteIllustrationDataProvider
{
    /** @var InventoryClient */
    private InventoryClient $inventoryClient;

    public function __construct(InventoryClient $inventoryClient)
    {
        $this->inventoryClient = $inventoryClient;
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
}