<?php

namespace App\MessageHandler;

use App\Service\ReceiptService;
use App\Message\GenerateReceiptFromFiscalYearMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateReceiptFromFiscalYearMessageHandler implements MessageHandlerInterface
{
    private $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function __invoke(GenerateReceiptFromFiscalYearMessage $generateReceiptFromFiscalYearMessage)
    {
        $fiscalYear = $generateReceiptFromFiscalYearMessage->getFiscalYear();
        $this->receiptService->generateTaxReceiptPdfFromFiscalYear($fiscalYear);
    }
}