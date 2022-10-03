<?php

declare(strict_types=1);

use PHPStan\Type\ArrayType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use Rector\Arguments\NodeAnalyzer\ArgumentAddingScope;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\Namespace_\RenameNamespaceRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\ValueObject\AddParamTypeDeclaration;

return static function (RectorConfig $rectorConfig): void {
    /*
     * Refactor service factory
     */
    $rectorConfig->ruleWithConfiguration(
        RenameMethodRector::class,
        [
            // rename method createService() to __invoke() in FactoryInterface
            new MethodCallRename(
                'Zend\\ServiceManager\\FactoryInterface',
                'createService',
                '__invoke'
            ),

            // rename the method `canCreateServiceWithName()` to `canCreate()` in AbstractFactoryInterface
            new MethodCallRename(
                'Zend\\ServiceManager\\AbstractFactoryInterface',
                'canCreateServiceWithName',
                'canCreate'
            ),
        ]
    );
    $rectorConfig->ruleWithConfiguration(
        AddParamTypeDeclarationRector::class,
        [
            // add type hint to $container argument
            new AddParamTypeDeclaration(
                'Zend\\ServiceManager\\FactoryInterface',
                '__invoke',
                0,
                new ObjectType('Interop\\Container\\ContainerInterface')
            ),
        ]
    );
    $rectorConfig->ruleWithConfiguration(
        RenameNamespaceRector::class,
        [
            // replace deprecated interface
            'Zend\\ServiceManager\\FactoryInterface' => 'Zend\\ServiceManager\\Factory\\FactoryInterface',
            'Zend\\ServiceManager\\AbstractFactoryInterface' => 'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
            'Zend\\Mvc\\Router\\Http\\Segment' => 'Zend\\Router\\Http\\Segment',
            'Zend\\Mvc\\Router\\Http\\Literal' => 'Zend\\Router\\Http\\Literal',

            // exceptions
            'Zend\\Mvc\\Router\\Exception\\InvalidArgumentException' => 'Zend\\Router\\Exception\\InvalidArgumentException',
        ]
    );
    $rectorConfig->ruleWithConfiguration(
        ArgumentAdderRector::class,
        [
            // add the `$requestedName` as a second argument
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\FactoryInterface',
                '__invoke',
                1,
                'requestedName',
                null,
                new MixedType()
            ),
            // add the optional `array $options = null` argument as a final argument
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\FactoryInterface',
                '__invoke',
                2,
                'options',
                null,
                new ArrayType(new MixedType(), new MixedType())
            ),
        ]
    );

    /*
     * Refactor event manager
     */
    $rectorConfig->ruleWithConfiguration(
        ArgumentAdderRector::class,
        [
            // add $priority as a second argument
            new ArgumentAdder(
                'Zend\\EventManager\\ListenerAggregateInterface',
                'attach',
                1,
                'priority',
                1,
                null,
                ArgumentAddingScope::SCOPE_CLASS_METHOD
            ),
        ]
    );
};
