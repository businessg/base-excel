<?php

declare(strict_types=1);

namespace BusinessG\BaseExcel\Data\Export;

use BusinessG\BaseExcel\Data\BaseObject;

class ExportData extends BaseObject
{
    /** @var \Psr\Http\Message\ResponseInterface|object|string */
    public mixed $response = '';

    public string $token = '';

    public function getResponse(): mixed
    {
        return $this->response;
    }

    public function __serialize(): array
    {
        return [
            'response' => is_string($this->response) ? $this->response : '',
            'token' => $this->token,
        ];
    }
}
