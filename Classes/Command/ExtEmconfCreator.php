<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Extension;

use Nette\PhpGenerator\Dumper;
use StefanFroemken\ExtKickstarter\Information\ExtensionInformation;

class ExtEmconfCreator implements ExtensionCreatorInterface
{
    public function create(ExtensionInformation $extensionInformation): void
    {
        file_put_contents(
            $extensionInformation->getExtensionPath() . 'ext_emconf.php',
            $this->getFileContent($extensionInformation),
        );
    }

    private function getFileContent(ExtensionInformation $configurator): string
    {
        $dumper = new Dumper();
        $dumper->wrapLength = 30;
        $dumper->indentation = '    ';

        $configuration = [
            'title' => $configurator->getTitle(),
            'description' => $configurator->getDescription(),
            'category' => $configurator->getCategory(),
            'state' => $configurator->getState(),
            'author' => $configurator->getAuthor(),
            'author_email' => $configurator->getAuthorEmail(),
            'author_company' => $configurator->getAuthorCompany(),
            'version' => $configurator->getVersion(),
            'constraints' => [
                'depends' => [
                    'typo3' => '12.4.0-12.4.99',
                ],
                'conflicts' => [],
                'suggests' => [],
            ],
        ];

        $exportedArray = $dumper->dump($configuration);

        return sprintf($this->getTemplate(), $exportedArray);
    }

    private function getTemplate(): string
    {
        return <<<'PHP'
<?php

$EM_CONF[$_EXTKEY] = %s;

PHP;
    }
}
