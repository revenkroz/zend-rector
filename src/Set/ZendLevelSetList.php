<?php

declare(strict_types=1);

namespace Revenkroz\ZendRector\Set;

use Rector\Set\Contract\SetListInterface;

final class ZendLevelSetList implements SetListInterface
{
    /** @var string */
    /* final */ public const UP_TO_ZEND_3 = __DIR__ . '/../../config/sets/zend/level/up-to-zend-3.php';
}
