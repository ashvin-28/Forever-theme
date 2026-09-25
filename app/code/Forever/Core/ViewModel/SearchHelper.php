<?php

namespace Forever\Core\ViewModel;

use Magento\Search\Helper\Data as SearchHelperData;

/**
 * View model wrapper exposing the search helper to templates without using the object manager
 */
class SearchHelper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var SearchHelperData
     */
    protected $searchHelper;

    /**
     * @param SearchHelperData $searchHelper
     */
    public function __construct(SearchHelperData $searchHelper)
    {
        $this->searchHelper = $searchHelper;
    }

    /**
     * Get the search helper instance
     *
     * @return SearchHelperData
     */
    public function getSearchHelper(): SearchHelperData
    {
        return $this->searchHelper;
    }
}
