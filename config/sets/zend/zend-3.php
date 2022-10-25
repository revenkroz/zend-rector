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
use Revenkroz\ZendRector\Rector\Zend3\AddModulesToConfigRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(
        RenameMethodRector::class,
        [
            /*
             * Refactor service factory
             */
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

            // rename the method `createServiceWithName()` to `__invoke()` in AbstractFactoryInterface
            new MethodCallRename(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
                'createServiceWithName',
                '__invoke'
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
            new AddParamTypeDeclaration(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
                '__invoke',
                0,
                new ObjectType('Interop\\Container\\ContainerInterface')
            ),
            new AddParamTypeDeclaration(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
                'canCreate',
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
            'Zend\\Mvc\\Service\\TranslatorServiceFactory' => 'Zend\\I18n\\Translator\\TranslatorServiceFactory',
            'Zend\\Mvc\\Controller\\AbstractConsoleController' => 'Zend\\Mvc\\Console\\Controller\\AbstractConsoleController',

            // exceptions
            'Zend\\Mvc\\Router\\Exception\\InvalidArgumentException' => 'Zend\\Router\\Exception\\InvalidArgumentException',
        ]
    );
    $rectorConfig->ruleWithConfiguration(
        ArgumentAdderRector::class,
        [
            /*
             * Service Manager
             */
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\FactoryInterface',
                '__invoke',
                0,
                'container',
                null,
                new ObjectType('Interop\\Container\\ContainerInterface')
            ),
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
                '__invoke',
                0,
                'container',
                null,
                new ObjectType('Interop\\Container\\ContainerInterface')
            ),
            // add the `$requestedName` as a second argument
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\FactoryInterface',
                '__invoke',
                1,
                'requestedName',
                null,
                new MixedType()
            ),
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
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
            new ArgumentAdder(
                'Zend\\ServiceManager\\Factory\\AbstractFactoryInterface',
                '__invoke',
                2,
                'options',
                null,
                new ArrayType(new MixedType(), new MixedType())
            ),

            /*
             * Event manager
             */
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
    $rectorConfig->ruleWithConfiguration(AddModulesToConfigRector::class, AddModulesToConfigRector::COMMON_MODULES);
};
