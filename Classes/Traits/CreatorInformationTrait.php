<?php

namespace FriendsOfTYPO3\Kickstarter\Traits;

use FriendsOfTYPO3\Kickstarter\Enums\FileModificationType;
use FriendsOfTYPO3\Kickstarter\Information\CreatorInformation;
use Symfony\Component\Console\Style\SymfonyStyle;

trait CreatorInformationTrait
{
    private function printCreatorInformation(CreatorInformation $creatorInformation, SymfonyStyle $io): void
    {
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
