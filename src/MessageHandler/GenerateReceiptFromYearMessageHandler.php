<?php

namespace App\MessageHandler;

use App\Service\ReceiptService;
use App\Message\GenerateReceiptFromYearMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateReceiptFromYearMessageHandler implements MessageHandlerInterface
{
    private $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function __invoke(GenerateReceiptFromYearMessage $generateReceiptFromYearMessage)
    {
        $receiptsGroupingFileId = $generateReceiptFromYearMessage->getReceiptsGroupingFileId();
        $userId = $generateReceiptFromYearMessage->getUserId();
        $this->receiptService->generateTaxReceiptPdfFromYear($receiptsGroupingFileId, $userId);
    }
}