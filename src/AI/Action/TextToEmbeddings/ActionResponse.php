<?php

namespace App\AI\Action\TextToEmbeddings;

use App\AI\Action\DataType\Embeddings;
use Ibexa\Contracts\ConnectorAi\ActionResponseInterface;
use Ibexa\Contracts\ConnectorAi\DataType;

class ActionResponse implements ActionResponseInterface
{
    public function __construct(
        private Embeddings $embeddings
    ) {
    }

    public function getOutput(): DataType
    {
        return $this->embeddings;
    }
}
