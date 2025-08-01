<?php

namespace StefanFroemken\ExtKickstarter\Information;

use StefanFroemken\ExtKickstarter\Enums\FileModificationType;

readonly class FileModificationInformation
{
    public function __construct(
        private string $path,
        private FileModificationType $fileModificationType,
        private string $message = '',
    ) {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFileModificationType(): FileModificationType
    {
        return $this->fileModificationType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
