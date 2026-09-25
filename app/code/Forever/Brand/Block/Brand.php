<?php

namespace Forever\Brand\Block;

use Forever\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Brand extends Template
{
    /**
     * This label won't be displayed in the frontend block
     */
    public const MAIN_LABEL = 'Default';

    /**
     * Config path for module enable
     */
    public const MODULE_ENABLE = 'brand/general/enable';

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Brand constructor
     *
     * @param Template\Context $context
     * @param CollectionFactory $collectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * Get All Brands
     *
     * @return DataObject[]
     */
    public function getItems()
    {
        $questionCollection = $this->collectionFactory->create();
        $questionCollection->addFieldToFilter('main_table.status', 1);

        return $questionCollection->getItems();
    }

    /**
     * Get media URL
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get module enable configuration value
     *
     * @return mixed
     */
    public function getConfigValue()
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::MODULE_ENABLE, $storeScope);
    }
}
