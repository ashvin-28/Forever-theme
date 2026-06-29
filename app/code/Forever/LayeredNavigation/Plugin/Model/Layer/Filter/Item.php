<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Plugin\Model\Layer\Filter;

use Forever\LayeredNavigation\Helper\Configdata;
use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;

class Item
{
    /** @var UrlInterface */
    protected $_url;

    /** @var Pager */
    protected $_htmlPagerBlock;

    /** @var RequestInterface */
    protected $_request;

    /** @var Configdata */
    protected $_moduleHelper;

    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        RequestInterface $request,
        Configdata $moduleHelper
    ) {
        $this->_url = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_request = $request;
        $this->_moduleHelper = $moduleHelper;
    }

    public function aroundGetUrl(FilterItem $item, callable $proceed)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed();
        }

        $requestVar = $item->getFilter()->getRequestVar();

        // Price filter ke liye early return — getValue() array hota hai
        if ($requestVar === 'price') {
            $query = [
                $requestVar => '{price_start}-{price_end}',
                $this->_htmlPagerBlock->getPageVarName() => null,
            ];

            return $this->_url->getUrl('*/*/*', [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query,
            ]);
        }

        $value = [];
        $requestValue = $this->_request->getParam($requestVar);
        if (is_string($requestValue) && $requestValue !== '') {
            $value = explode(',', $requestValue);
        }

        $value[] = (string)$item->getValue();
        $value = array_values(array_unique(array_filter($value, 'strlen')));

        $query = [
            $requestVar => implode(',', $value),
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];

        if (isset($query['cat'])) {
            $cat = explode(',', (string)$query['cat']);
            $query['cat'] = end($cat);
        }

        return $this->_url->getUrl('*/*/*', [
            '_current'     => true,
            '_use_rewrite' => true,
            '_query'       => $query,
        ]);
    }

    public function aroundGetRemoveUrl(FilterItem $item, callable $proceed)
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed();
        }

        $requestVar = $item->getFilter()->getRequestVar();

        // Price filter ke liye early return — getValue() array hota hai, (string) cast crash karta hai
        if ($requestVar === 'price') {
            $query = [
                $requestVar => null,
                $this->_htmlPagerBlock->getPageVarName() => null,
            ];

            return $this->_url->getUrl('*/*/*', [
                '_current'     => true,
                '_use_rewrite' => true,
                '_query'       => $query,
                '_escape'      => true,
            ]);
        }

        $value = [];
        $requestValue = $this->_request->getParam($requestVar);
        if (is_string($requestValue) && $requestValue !== '') {
            $value = explode(',', $requestValue);
        }

        // Ab price nahi hai to getValue() safely string cast hoga
        $value = array_values(array_diff($value, [(string)$item->getValue()]));

        $query = [
            $requestVar => count($value) ? implode(',', $value) : $item->getFilter()->getResetValue(),
            $this->_htmlPagerBlock->getPageVarName() => null,
        ];

        return $this->_url->getUrl('*/*/*', [
            '_current'     => true,
            '_use_rewrite' => true,
            '_query'       => $query,
            '_escape'      => true,
        ]);
    }
}