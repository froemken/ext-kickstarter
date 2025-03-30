<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model;

class Link
{
    public function __construct(
        private readonly int $id,
        private readonly int $parentNodeId,
        private readonly int $targetNodeId,
        private readonly string $linkType,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentNodeId(): int
    {
        return $this->parentNodeId;
    }

    public function getTargetNodeId(): int
    {
        return $this->targetNodeId;
    }

    public function getLinkType(): string
    {
        return $this->linkType;
    }
}
