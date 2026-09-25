<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model;

use Forever\Blog\Model\ResourceModel\Tag as TagResourceModel;
use Magento\Framework\Model\AbstractModel;

class Tag extends AbstractModel
{
    /**
     * Initialize the tag resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(TagResourceModel::class);
    }
}
