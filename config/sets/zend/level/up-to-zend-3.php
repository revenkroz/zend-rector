<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Revenkroz\ZendRector\Set\ZendSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([ZendSetList::ZEND_3]);
};
