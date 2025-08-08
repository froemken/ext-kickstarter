<?php

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Context\CommandContext;
use StefanFroemken\ExtKickstarter\Enums\FileModificationType;
use StefanFroemken\ExtKickstarter\Information\CreatorInformation;

trait CreatorInformationTrait
{
    private function printCreatorInformation(CreatorInformation $creatorInformation, CommandContext $commandContext): void
    {
        $io = $commandContext->getIo();
        foreach ($creatorInformation->getFileModifications() as $fileModification) {
            switch ($fileModification->getFileModificationType()) {
                case FileModificationType::CREATED:
                    $io->success('File ' . $fileModification->getPath() . ' was created. ');
                    break;
                case FileModificationType::MODIFIED:
                    $io->success('File ' . $fileModification->getPath() . ' was modified. ');
                    break;
                case FileModificationType::NOT_MODIFIED:
                    $io->warning('File ' . $fileModification->getPath() . ' does not need to be modified:  ' . $fileModification->getMessage());
                    break;
                case FileModificationType::CREATION_FAILED:
                    $io->error('File ' . $fileModification->getPath() . ' could not be created: ' . $fileModification->getMessage());
                    break;
                case FileModificationType::MODIFICATION_FAILED:
                    $io->error('File ' . $fileModification->getPath() . ' could not be modified: ' . $fileModification->getMessage());
                    break;
                default:
                    $io->error('Something went wrong: ' . $fileModification->getMessage());
            }
        }
    }
}
