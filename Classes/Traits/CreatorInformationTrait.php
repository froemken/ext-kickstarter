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
            switch ($fileModification->fileModificationType) {
                case FileModificationType::CREATED:
                    $io->success('File ' . $fileModification->path . ' was created. ');
                    break;
                case FileModificationType::MODIFIED:
                    $io->success('File ' . $fileModification->path . ' was modified. ');
                    break;
                case FileModificationType::CREATION_FAILED:
                    $io->warning('File ' . $fileModification->path . ' could not be created: ' . $fileModification->message);
                    break;
                case FileModificationType::MODIFICATION_FAILED:
                    $io->warning('File ' . $fileModification->path . ' could not be modified: ' . $fileModification->message);
                    break;
                default:
                    $io->warning('Something went wrong: ' . $fileModification->message);
            }
        }
    }
}
