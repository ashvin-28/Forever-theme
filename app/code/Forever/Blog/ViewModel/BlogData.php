<?php

namespace Forever\Blog\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class BlogData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public const XML_PATH_EMAIL_RECIPIENT = 'blog/general/enable';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeScope;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeScope
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeScope
    ) {
        $this->storeScope = $storeScope;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get the blog module enable config value
     *
     * @return mixed
     */
    public function getConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $storeScope);
    }

    /**
     * Get the current store's base media URL
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $media_dir = $this->storeScope->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }
}
