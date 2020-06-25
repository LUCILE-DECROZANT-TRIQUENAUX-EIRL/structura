<?php

namespace App\MessageHandler;

use App\Service\ReceiptService;
use App\Message\GenerateReceiptFromTwoDatesMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateReceiptFromTwoDatesMessageHandler implements MessageHandlerInterface
{
    private $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function __invoke(GenerateReceiptFromTwoDatesMessage $generateReceiptFromTwoDatesMessage)
    {
        $receiptsGroupingFileId = $generateReceiptFromTwoDatesMessage->getReceiptsGroupingFileId();
        $userId = $generateReceiptFromTwoDatesMessage->getUserId();
        $this->receiptService->generateTaxReceiptPdfFromTwoDates($receiptsGroupingFileId, $userId);
    }
}