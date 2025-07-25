<?php

namespace StefanFroemken\ExtKickstarter\Information;

class CreatorInformation
{
    /**
     * @param FileModificationInformation[] $fileModifications
     */
    public function __construct(
        public array $fileModifications,
    ) {}
}
