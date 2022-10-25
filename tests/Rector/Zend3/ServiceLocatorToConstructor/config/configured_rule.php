<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Revenkroz\ZendRector\Rector\Zend3\ServiceLocatorToConstructorRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->import(__DIR__ . '/../../../../../config/config.php');

    $rectorConfig->rule(ServiceLocatorToConstructorRector::class);

    // for tests
    $rectorConfig->phpVersion(PhpVersion::PHP_72);
};
