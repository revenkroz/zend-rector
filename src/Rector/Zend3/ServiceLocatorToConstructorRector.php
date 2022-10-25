<?php

namespace Revenkroz\ZendRector\Rector\Zend3;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Rector\AbstractRector;
use Rector\Naming\Naming\PropertyNaming;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PostRector\Collector\PropertyToAddCollector;
use Rector\PostRector\ValueObject\PropertyMetadata;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Rector\StaticTypeMapper\ValueObject\Type\ShortenedObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ServiceLocatorToConstructorRector extends AbstractRector
{
    /** @var PropertyToAddCollector */
    private $propertyToAddCollector;

    /** @var PropertyNaming */
    private $propertyNaming;

    /** @var PhpDocTagRemover */
    private $phpDocTagRemover;

    public function __construct(PropertyToAddCollector $propertyToAddCollector, PropertyNaming $propertyNaming, PhpDocTagRemover $phpDocTagRemover)
    {
        $this->propertyToAddCollector = $propertyToAddCollector;
        $this->propertyNaming = $propertyNaming;
        $this->phpDocTagRemover = $phpDocTagRemover;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Turns fetching of dependencies via `$this->serviceLocator->get()` to constructor injection',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
final class MyController extends AbstractActionController
{
    public function someAction()
    {
        /** @var DocumentService $documentService */
        $documentService = $this->serviceLocator->get('application.application.document.DocumentService');
        $documentService->doSomething();

        /** @var PaperService $paperService */
        $paperService = $this->serviceLocator->get('application.application.paper.PaperService');
        $paperService->doSomething();
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
final class MyController extends AbstractActionController
{
    private DocumentService $documentService;
    private PaperService $paperService;
    public function __construct(DocumentService $documentService, PaperService $paperService)
    {
        $this->documentService = $documentService;
        $this->paperService = $paperService;
    }

    public function someAction()
    {
        $documentService = $this->documentService;
        $documentService->doSomething();

        $paperService = $this->paperService;
        $paperService->doSomething();
    }
}
CODE_SAMPLE
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->checkNode($node)) {
            $assign = $node->getAttribute(AttributeKey::PARENT_NODE);
            if (!$assign instanceof Assign) {
                return null;
            }

            $expression = $assign->getAttribute(AttributeKey::PARENT_NODE);
            if (!$expression instanceof Expression) {
                return null;
            }

            $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($expression);

            $type = $this->getType($assign->var);
            if ($type instanceof ShortenedObjectType) {
                $className = $type->getFullyQualifiedName();
            } elseif ($type instanceof FullyQualifiedObjectType) {
                $className = $type->getClassName();
            } else {
                return null;
            }

            $class = $this->betterNodeFinder->findParentType($node, Class_::class);
            if (!$class instanceof Class_) {
                return null;
            }

            $varTagValueNode = $phpDocInfo->getVarTagValueNode();
            if ($varTagValueNode instanceof VarTagValueNode) {
                $this->phpDocTagRemover->removeTagValueFromNode($phpDocInfo, $varTagValueNode);
            }

            $objectType = new ObjectType($className);
            $propertyName = $this->propertyNaming->fqnToVariableName($objectType);

            $propertyMetadata = new PropertyMetadata($propertyName, $objectType, Class_::MODIFIER_PRIVATE);
            $this->propertyToAddCollector->addPropertyToClass($class, $propertyMetadata);

            return $this->nodeFactory->createPropertyFetch('this', $propertyName);
        }

        return null;
    }

    private function checkNode(Node $node): bool
    {
        $standard = new Standard();
        $code = $standard->prettyPrint([$node]);
        $thisCall = 0 === strpos($code, '$this->');

        return
            // 1. Case '$this->serviceLocator'
            ($thisCall && \is_object($node->var->name) && 'serviceLocator' === $this->getName($node->var->name) && $this->isName($node->name, 'get')) ||
            // 2. Case '$this->getServiceLocator()'
            ($thisCall && \is_object($node->var->name) && 'getServiceLocator' === $this->getName($node->var->name) && $this->isName($node->name, 'get')) ||
            // 3. Case class exists
            ($thisCall && $this->isObjectType($node->var, new ObjectType('Zend\ServiceManager\ServiceLocatorInterface')) && $this->isName($node->name, 'get'))
        ;
    }
}
