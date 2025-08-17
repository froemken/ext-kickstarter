<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validatation;

use Attribute;
use FriendsOfTYPO3\Kickstarter\Command\Input\Validator\ValidatorInterface;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class UseValidator
{
    /** @param class-string<ValidatorInterface> $serviceId */
    public function __construct(public string $serviceId) {}
}
