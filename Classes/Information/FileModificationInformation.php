<?php

namespace StefanFroemken\ExtKickstarter\Information;

use StefanFroemken\ExtKickstarter\Enums\FileModificationType;

readonly class FileModificationInformation
{
    public function __construct(
        public string $path,
        public FileModificationType $fileModificationType,
        public string $message = '',
    ) {}
}
