<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Controller\Adminhtml\Tag;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
    /**
     * Check whether the current admin user is allowed to save tags
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->_authorization->isAllowed('Forever_Blog::save');
    }

    /**
     * Forward to the edit action to render the new tag form
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
