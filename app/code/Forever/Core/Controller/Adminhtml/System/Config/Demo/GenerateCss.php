<?php

namespace Forever\Core\Controller\Adminhtml\System\Config\Demo;

use Magento\Framework\Controller\ResultFactory;

class GenerateCss extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_publicActions = ['generatecss'];

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);

        $this->_objectManager = $objectManager;
    }

    /**
     * Generate the store's design CSS file and redirect back to the referring page
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->_objectManager->get(\Forever\Core\Model\Cssconfig\Generator::class)->generateCss('css', '', '');

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
