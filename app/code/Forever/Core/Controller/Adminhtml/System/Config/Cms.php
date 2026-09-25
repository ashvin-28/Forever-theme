<?php

namespace Forever\Core\Controller\Adminhtml\System\Config;

abstract class Cms extends \Magento\Backend\App\Action
{
    /**
     * Import CMS sample data for the requested import type
     *
     * @return \Magento\Framework\DataObject
     */
    protected function _import()
    {
        return $this->_objectManager->get(\Forever\Core\Model\Import\Cms::class)
            ->importCms($this->getRequest()->getParam('import_type'));
    }
}
