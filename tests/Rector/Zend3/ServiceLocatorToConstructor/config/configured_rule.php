<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Revenkroz\ZendRector\Rector\Zend3\ServiceLocatorToConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(ServiceLocatorToConstructorRector::class);
};
