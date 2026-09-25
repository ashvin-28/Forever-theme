<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model;

use Forever\Blog\Model\ResourceModel\Blog as BlogResourceModel;
use Magento\Framework\Model\AbstractModel;

class Blog extends AbstractModel
{
    /**
     * Initialize the blog post resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(BlogResourceModel::class);
    }
}
