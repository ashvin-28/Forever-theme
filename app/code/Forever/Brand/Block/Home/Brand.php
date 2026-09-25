<?php

namespace Forever\Brand\Block\Home;

use Forever\Brand\Model\ResourceModel\Brand\Collection;
use Forever\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Brand extends Template
{
    /**
     * Config path for enabling the brand module on homepage
     */
    private const XML_PATH_BRAND_ENABLED = 'brand/general/enable';

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $brandCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param Context $context
     * @param CollectionFactory $brandCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $brandCollectionFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Get all active brand items
     *
     * @return Collection
     */
    public function getItems(): Collection
    {
        /** @var Collection $collection */
        $collection = $this->brandCollectionFactory->create();
        $collection->addFieldToFilter('status', 1);
        return $collection;
    }

    /**
     * Get the media URL for a brand image
     *
     * @param string|null $imageName
     * @return string
     */
    public function getBrandImageUrl(?string $imageName): string
    {
        if (!$imageName) {
            return '';
        }
        try {
            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            return $mediaUrl . 'brand/image/' . ltrim($imageName, '/');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Get config value for brand module enable/disable
     *
     * @return int
     */
    public function getConfigValue(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_BRAND_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
