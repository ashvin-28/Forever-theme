<?php

declare(strict_types=1);

namespace Forever\Faq\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * FAQ index page controller
 */
class Index implements HttpGetActionInterface
{
    /**
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        private readonly PageFactory $resultPageFactory
    ) {
    }

    /**
     * Display FAQ page
     *
     * @return Page
     */
    public function execute(): Page
    {
        return $this->resultPageFactory->create();
    }
}
