<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model\Node\Main;

use StefanFroemken\ExtKickstarter\Model\AbstractNode;

class AuthorNode extends AbstractNode
{
    public function getAuthorName(): string
    {
        return $this->getProperties()['name'] ?? '';
    }

    public function getAuthorEmail(): string
    {
        return $this->getProperties()['email'] ?? '';
    }

    public function getAuthorCompany(): string
    {
        return $this->getProperties()['company'] ?? '';
    }

    public function getAuthorRole(): string
    {
        return $this->getProperties()['role'] ?? '';
    }
}
