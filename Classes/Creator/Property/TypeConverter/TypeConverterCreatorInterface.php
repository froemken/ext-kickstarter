<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Property\TypeConverter;

use StefanFroemken\ExtKickstarter\Information\TypeConverterInformation;

interface TypeConverterCreatorInterface
{
    public function create(TypeConverterInformation $typeConverterInformation): void;
}
