<?php

namespace StefanFroemken\ExtKickstarter\Enums;

enum FileModificationType
{
    case CREATED;
    case CREATION_FAILED;
    case MODIFIED;
    case NOT_MODIFIED;
    case MODIFICATION_FAILED;
    case ABORTED;
}
