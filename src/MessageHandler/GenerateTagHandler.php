<?php

namespace App\MessageHandler;

use App\Message\GenerateTagMessage;
use App\Service\TagService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GenerateTagHandler implements MessageHandlerInterface
{
    private $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function __invoke(GenerateTagMessage $generateTagMessage)
    {
        $people = $generateTagMessage->getPeople();
        $this->tagService->generateTagsPdf($people);
    }
}
