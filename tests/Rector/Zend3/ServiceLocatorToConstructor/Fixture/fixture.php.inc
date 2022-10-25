<?php

namespace Revenkroz\ZendRector\Tests\Rector\Zend3\ServiceLocatorToConstructor\Fixture;

final class MyController
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
?>
-----
<?php

namespace Revenkroz\ZendRector\Tests\Rector\Zend3\ServiceLocatorToConstructor\Fixture;

final class MyController
{
    /** @var DocumentService */
    private $documentService;
    /** @var PaperService */
    private $paperService;
    public function __construct(\Revenkroz\ZendRector\Tests\Rector\Zend3\ServiceLocatorToConstructor\Fixture\DocumentService $documentService, \Revenkroz\ZendRector\Tests\Rector\Zend3\ServiceLocatorToConstructor\Fixture\PaperService $paperService)
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
?>
