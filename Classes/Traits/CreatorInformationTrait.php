<?php

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Enums\FileModificationType;
use StefanFroemken\ExtKickstarter\Information\CreatorInformation;
use Symfony\Component\Console\Style\SymfonyStyle;

trait CreatorInformationTrait
{
    private function printCreatorInformation(CreatorInformation $creatorInformation, SymfonyStyle $io): void
    {
        foreach ($creatorInformation->fileModifications as $fileModification) {
            switch ($fileModification->getFileModificationType()) {
                case FileModificationType::CREATED:
                    $io->success('File ' . $fileModification->getPath() . ' was created. ');
                    break;
                case FileModificationType::MODIFIED:
                    $io->success('File ' . $fileModification->getPath() . ' was modified. ');
                    break;
                case FileModificationType::CREATION_FAILED:
                    $io->warning('File ' . $fileModification->getPath() . ' could not be created: ' . $fileModification->getMessage());
                    break;
                case FileModificationType::MODIFICATION_FAILED:
                    $io->warning('File ' . $fileModification->getPath() . ' could not be modified: ' . $fileModification->getMessage());
                    break;
                default:
                    $io->warning('Something went wrong: ' . $fileModification->getMessage());
            }
        }
    }
}
