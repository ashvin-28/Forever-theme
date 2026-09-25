<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model\ResourceModel;

class Blog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize the blog post resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forever_blog', 'blog_id');
    }
}
