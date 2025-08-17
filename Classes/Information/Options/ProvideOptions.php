<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Options;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ProvideOptions
{
    /** @param class-string<OptionsInterface> $serviceId */
    public function __construct(public string $serviceId) {}
}
