<?php

namespace FriendsOfTYPO3\Kickstarter\Information\DefaultValue;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ProvideDefaultValue
{
    /** @param class-string<DefaultValueInterface> $serviceId */
    public function __construct(public string $serviceId) {}
}
