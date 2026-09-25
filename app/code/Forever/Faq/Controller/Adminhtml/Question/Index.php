<?php

declare(strict_types=1);

namespace Forever\Faq\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Page;

/**
 * Displays the FAQ question grid.
 */
class Index extends Action
{
    /**
     * Execute index action.
     *
     * @return Page
     */
    public function execute(): Page
    {
        /** @var Page $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $result;
    }
}
