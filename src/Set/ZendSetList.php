<?php

declare(strict_types=1);

namespace Revenkroz\ZendRector\Set;

use Rector\Set\Contract\SetListInterface;

final class ZendSetList implements SetListInterface
{
    /**
     * @var string
     */
    /* final */ public const ZEND_3 = __DIR__ . '/../../config/sets/zend/zend-3.php';
}
