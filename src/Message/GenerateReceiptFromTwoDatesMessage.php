<?php

namespace App\Message;


class GenerateReceiptFromTwoDatesMessage
{
    private $receiptsGroupingFileId;
    private $userId;

    public function __construct(int $receiptsGroupingFileId, int $userId)
    {
        $this->receiptsGroupingFileId = $receiptsGroupingFileId;
        $this->userId = $userId;
    }

    public function getReceiptsGroupingFileId(): int
    {
        return $this->receiptsGroupingFileId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}