<?php

namespace Forever\Blog\Block\Sidebar;

use Magento\Framework\View\Element\Template;
use Forever\Blog\Model\ResourceModel\Blog\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Forever\Blog\Model\BlogFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\Catalog\Helper\ImageFactory;

/**
 * Class MostView
 * package forever\Blog\Block\Sidebar
 */
class MostView extends Template
{
    public const BLOG_RECENT_POST = 'blog/sidebar/number_recent_posts';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var BlogFactory
     */
    protected $blogFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storManager;

    /**
     * @var ImageFactory
     */
    protected $helperImageFactory;

    /**
     * @var Repository
     */
    protected $assetRepos;

    /**
     * @param Template\Context      $context
     * @param CollectionFactory     $collectionFactory
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storManager
     * @param BlogFactory           $blogFactory
     * @param Repository            $assetRepos
     * @param ImageFactory          $helperImageFactory
     * @param array                 $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storManager,
        BlogFactory $blogFactory,
        Repository $assetRepos,
        ImageFactory $helperImageFactory,
        array $data = []
    ) {
        $this->assetRepos = $assetRepos;
        $this->helperImageFactory = $helperImageFactory;
        $this->collectionFactory = $collectionFactory;
        $this->scopeConfig = $scopeConfig;
        
        $this->storManager = $storManager;
        $this->blogFactory = $blogFactory;

        parent::__construct($context, $data);
    }

    /**
     * Get a store config value for the given path
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigData($path)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $storeScope);
    }

    /**
     * Get the active blog post collection
     *
     * @return \Forever\Blog\Model\ResourceModel\Blog\Collection
     */
    public function blogCollection()
    {
        $blogCollection = $this->collectionFactory->create()
        ->addFieldToSelect('*')
        ->addFieldToFilter('status', ['eq' => '1']);
        return $blogCollection;
    }

    /**
     * Get the recently added blog post collection
     *
     * @return \Forever\Blog\Model\ResourceModel\Blog\Collection
     */
    public function getRecentPost()
    {
        $collection = $this->blogCollection()->setOrder('publish_time', 'DESC');
         
        $collection->getSelect()->limit((int)$this->getConfigData(self::BLOG_RECENT_POST) ?: 4);
        return $collection;
    }

    /**
     * Get the current store's base media URL
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $media_dir = $this->storManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $media_dir;
    }

    /**
     * Get the blog post view URL
     *
     * @param string $viewUrlKey
     * @return string
     */
    public function getViewUrl($viewUrlKey)
    {
        $baseUrl = $this->storManager->getStore()->getBaseUrl();
        $getViewUrl = $baseUrl . 'blog/index/view/' . $viewUrlKey;
        return $getViewUrl;
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
