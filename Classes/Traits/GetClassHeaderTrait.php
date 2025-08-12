<?php

declare(strict_types=1);

/*
 * This file is part of the package friendsoftypo3/kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace FriendsOfTYPO3\Kickstarter\Traits;

use FriendsOfTYPO3\Kickstarter\Model\Node\Main\ExtensionNode;

trait GetClassHeaderTrait
{
    private function getClassHeader(ExtensionNode $extensionNode): string
    {
        return str_replace(
            '{{COMPOSER_NAME}}',
            $extensionNode->getComposerName(),
            $this->getClassHeaderTemplate(),
        );
    }

    private function getClassHeaderTemplate(): string
    {
        return <<<'EOT'
/*
 * This file is part of the package {{COMPOSER_NAME}}.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
EOT;
    }
}
