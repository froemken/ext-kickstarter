<?php

namespace FriendsOfTYPO3\Kickstarter\Information\Normalization;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class UseNormalizer
{
    /** @param class-string<NormalizerInterface> $serviceId */
    public function __construct(public string $serviceId) {}
}
