<?php

namespace Revenkroz\ZendRector\Tests\Rector\Zend3\AddModulesToConfig;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

class AddModulesToConfigRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test($file): void
    {
        if ($file instanceof SmartFileInfo) {
            $this->doTestFileInfo($file);
        } else {
            $this->doTestFile($file);
        }
    }

    /**
     * @return Iterator<string>
     */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
