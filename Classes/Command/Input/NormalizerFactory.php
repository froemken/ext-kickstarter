<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command\Input;

use StefanFroemken\ExtKickstarter\Command\Input\Normalizer\NormalizerInterface;

readonly class NormalizerFactory
{
    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function __construct(
        private iterable $normalizers,
    ) {}

    public function getNormalizer(
        string $propertyName,
    ): ?NormalizerInterface {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->getArgumentName() === $propertyName) {
                return $normalizer;
            }
        }

        return null;
    }
}
