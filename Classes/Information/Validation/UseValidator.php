<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Validation;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UseValidator
{
    /** @param class-string<ValidatorInterface> $serviceId */
    public function __construct(public string $serviceId) {}
}
