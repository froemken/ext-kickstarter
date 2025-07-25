<?php

namespace StefanFroemken\ExtKickstarter\Traits;

use StefanFroemken\ExtKickstarter\Enums\FileModificationType;
use StefanFroemken\ExtKickstarter\Information\FileModificationInformation;
use Symfony\Component\Console\Style\SymfonyStyle;

trait FileModificationInformationTrait
{
    /**
     * @param FileModificationInformation[] $fileModifications
     */
    private function printFileModificationInformation(array $fileModifications, SymfonyStyle $io): void
    {
        foreach ($fileModifications as $fileModification) {
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
