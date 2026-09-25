<?php

namespace Forever\Blog\Block;

use Magento\Framework\View\Element\Template;
use Magento\Theme\Block\Html\Pager;

/**
 * @Class PostList
 */
class PostList extends Template
{
    /**
     * @var \Forever\Blog\Model\ResourceModel\Blog\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storManager;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $helperImageFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepos;

    /**
     * @param Template\Context $context
     * @param \Forever\Blog\Model\ResourceModel\Blog\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storManager
     * @param \Magento\Framework\View\Asset\Repository $assetRepos
     * @param \Magento\Catalog\Helper\ImageFactory $helperImageFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Forever\Blog\Model\ResourceModel\Blog\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storManager,
        \Magento\Framework\View\Asset\Repository $assetRepos,
        \Magento\Catalog\Helper\ImageFactory $helperImageFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->storManager = $storManager;
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
    }

    /**
     * Get the paginated blog post collection
     *
     * @return \Forever\Blog\Model\ResourceModel\Blog\Collection
     */
    public function getCollection()
    {
        $perPageValue = $this->scopeConfig->getValue(
            'blog/general/list_per_page',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : $perPageValue;

        $collection = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', ['eq' => '1']);
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);

        return $collection;
    }

    /**
     * Prepare the pager child block for the blog post list
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Pagination'));
        $configValue = $this->scopeConfig->getValue(
            'blog/general/list_per_page_values',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $pageLimit = explode(',', $configValue);
        $setLimit = [];
        foreach ($pageLimit as $key => $value) {
                $setLimit[$value] = $value;
        }
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                Pager::class,
                'custom.history.pager'
            )->setAvailableLimit($setLimit)
            ->setShowPerPage(true)->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * Get the pager block html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get the blog post view URL
     *
     * @param string $viewUrlKey
     * @return string
     */
    public function getViewUrl($viewUrlKey)
    {
        if ($viewUrlKey == "#") {
            return "javascript: void(0)";
        } else {
            $baseUrl = $this->storManager->getStore()->getBaseUrl();
            $getViewUrl = $baseUrl . 'blog/index/view/' . $viewUrlKey;
            return $getViewUrl;
        }
    }

    /**
     * Get the placeholder image URL
     *
     * @return string
     */
    public function getPlaceHolderImage()
    {
        $imagePlaceholder = $this->helperImageFactory->create();
        return $this->assetRepos->getUrl($imagePlaceholder->getPlaceholder('small_image'));
    }
}
