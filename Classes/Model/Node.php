<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Model;

class Node
{
    public function __construct(
        private readonly int $id,
        private readonly string $type,
        private readonly ?string $title,
        private readonly \SplObjectStorage $inputs,
        private readonly \SplObjectStorage $outputs,
        private readonly array $properties
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    /**
     * @return \SplObjectStorage|Input[]
     */
    public function getInputs(): \SplObjectStorage
    {
        return $this->inputs;
    }

    /**
     * @return \SplObjectStorage|Output[]
     */
    public function getOutputs(): \SplObjectStorage
    {
        return $this->outputs;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}