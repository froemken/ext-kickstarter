<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model;

class Input
{
    public function __construct(
        private readonly ?int $link,
        private readonly string $type,
        private readonly string $name,
    ) {}

    public function getLink(): int
    {
        return $this->link ?? 0;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
