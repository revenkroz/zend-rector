<?php

namespace Revenkroz\ZendRector\Rector\Zend3;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Core\Rector\AbstractRector;
use Revenkroz\ZendRector\Rector\Traits\ArrayTrait;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class AddModulesToConfigRector extends AbstractRector implements ConfigurableRectorInterface
{
    use ArrayTrait;

    public const COMMON_MODULES = [
        'Zend\I18n',
        'Zend\Mvc\Console',
        'Zend\Log',
        'Zend\Router',
    ];

    /** @var array */
    private $newModules = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add modules to application config', [new CodeSample(
            <<<'CODE_SAMPLE'
return [
    // ...
    'modules' => [
        'ErrorHandler',
        'EventBus',
        'DoctrineModule',
        'DoctrineORMModule',
        'Application',
   ],
   // ...
];
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
return [
    // ...
    'modules' => [
        'Zend\I18n',
        'Zend\Mvc\Console',
        'Zend\Log',
        'Zend\Router',
        'ErrorHandler',
        'EventBus',
        'DoctrineModule',
        'DoctrineORMModule',
        'Application',
   ],
   // ...
];
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [FileWithoutNamespace::class];
    }

    public function refactor(Node $node): ?Node
    {
        // check if the file is from config/ or contains 'fixture' in the name
        if (0 !== strpos($this->file->getFilePath(), 'config/') && false === strpos($this->file->getFilePath(), 'fixture')) {
            return null;
        }

        $somethingChanged = false;

        // process only files with first level return array
        foreach ($node->stmts as $stmt) {
            if (!$stmt instanceof Node\Stmt\Return_) {
                continue;
            }

            if (null === $stmt->expr) {
                return null;
            }

            $newNode = $this->refactorReturnArray($stmt->expr);
            if (null !== $newNode) {
                $stmt = $newNode;
                $somethingChanged = true;
            }
        }

        return $somethingChanged ? $node : null;
    }

    private function refactorReturnArray(Array_ $array): ?Node
    {
        $somethingChanged = false;
        $this->traverseNodesWithCallable($array, function (Node $node) use (&$somethingChanged): ?Node {
            if (!$node instanceof Array_) {
                return null;
            }

            $config = null;
            $configItem = $this->extractArrayItemByKey($node, 'modules');
            if (null !== $configItem) {
                $config = $configItem->value;
                if (!$config instanceof Array_) {
                    return null;
                }
            }

            // if modules is not set, do nothing
            if (null === $config) {
                return null;
            }

            // form list with modules
            $oldModules = $this->valueResolver->getValue($config);
            $modules = array_unique(array_merge($this->newModules, $oldModules));

            // if modules is not changed, do nothing
            if ([] === array_diff($modules, $oldModules)) {
                return null;
            }

            // reset modules array and fill
            $config->items = [];
            foreach ($modules as $module) {
                $config->items[] = new ArrayItem(new String_($module));
            }

            $somethingChanged = true;

            return $node;
        });

        return $somethingChanged ? $array : null;
    }

    public function configure(array $configuration): void
    {
        $this->newModules = $configuration;
    }
}
