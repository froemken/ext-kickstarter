<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use StefanFroemken\ExtKickstarter\Creator\FileManager;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtIconCreator implements ExtensionCreatorInterface
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {}

    public function create(ExtensionInformation $extensionInformation): void
    {
        $extIconPath = $extensionInformation->getExtensionPath() . 'Resources/Public/Icons/';
        GeneralUtility::mkdir_deep($extIconPath);

        $this->fileManager->createOrModifyFile(
            $extIconPath . 'Extension.svg',
            $this->getTemplate(),
            $extensionInformation->getCreatorInformation()
        );
    }

    private function getTemplate(): string
    {
        return <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="83.098" height="84.172" viewBox="43.201 42.122 83.098 84.172">
  <path fill="#FF8700" d="M106.074 100.128c-1.247.368-2.242.506-3.549.506-10.689 0-26.389-37.359-26.389-49.793 0-4.577 1.083-6.104 2.613-7.415-13.084 1.527-28.784 6.329-33.806 12.433-1.085 1.529-1.743 3.926-1.743 6.98 0 19.41 20.718 63.455 35.332 63.455 6.765.001 18.164-11.112 27.542-26.166M99.25 42.122c13.52 0 27.049 2.18 27.049 9.812 0 15.483-9.819 34.246-14.832 34.246-8.942 0-20.065-24.867-20.065-37.301.001-5.67 2.181-6.757 7.848-6.757"/>
</svg>
EOT;
    }
}
