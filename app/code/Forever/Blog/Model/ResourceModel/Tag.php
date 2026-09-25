<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forever\Blog\Model\ResourceModel;

class Tag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize the tag resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forever_blog_tag', 'tag_id');
    }
}
