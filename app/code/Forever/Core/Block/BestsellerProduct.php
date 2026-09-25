<?php
namespace Forever\Core\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product as ModelProduct;

class BestsellerProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    public const ISENABLE = 'themedesign/imageswitcher/enable';
    public const XML_PATH_BESTSELLER = 'bestseller/general/enable';
    public const XML_PRODUCT_ROW = 'bestseller/general/productscount';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $bestSellersCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Template processor instance
     *
     * @var \Magento\Framework\Filter\Template
     */
    protected $templateProcessor = null;

    /**
     * Attribute metadata configuration instance
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var \Forever\Core\Model\Config\Rows
     */
    protected $row;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperData;

    /**
     * @var \Forever\Productlabel\ViewModel\ProductLabelViewModel
     */
    protected $productLabelViewModel;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellersCollectionFactory
     * @param \Forever\Core\Model\Config\Rows $row
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Escaper $escaper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Block\Product\Context $gridcontext
     * @param \Magento\Catalog\Block\Product\ListProduct $listProductBlock
     * @param \Magento\Catalog\Helper\Image $helperData
     * @param \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel
     * @param \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestSellersCollectionFactory,
        \Forever\Core\Model\Config\Rows $row,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Block\Product\Context $gridcontext,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Helper\Image $helperData,
        \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel,
        \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->categoryFactory = $categoryFactory;
        $this->compareProduct = $context->getCompareProduct();
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->escaper = $escaper;
        $this->listProductBlock = $listProductBlock;
        $this->compareProduct = $gridcontext->getCompareProduct();
        $this->wishlistHelper = $gridcontext->getWishlistHelper();
        $this->row = $row;
        $this->helperData = $helperData;
        $this->productLabelViewModel = $productLabelViewModel;
        $this->authenticationviewmodel = $authenticationviewmodel;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BESTSELLER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the configured number of bestseller products to display
     *
     * @return string
     */
    public function rowProduct()
    {
        return $this->scopeConfig->getValue(
            self::XML_PRODUCT_ROW,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the compare product helper
     *
     * @return \Magento\Catalog\Helper\Product\Compare
     * @since 101.0.1
     */
    public function getCompareHelper()
    {
        return $this->compareProduct;
    }

    /**
     * Return best seller products
     *
     * @return mixed
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Return best seller products
     *
     * @return mixed
     */
    protected function _getProductCollection()
    {
        try {
            if ($this->_productCollection === null) {
                $this->_productCollection = $this->initializeProductCollection();
            }
            return $this->_productCollection;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    /**
     * Return best seller products
     *
     * @return mixed
     */
    private function initializeProductCollection()
    {
        try {
            $selectid = $this->rowProduct();
            $productIds = [];
            $bestSellers = $this->bestSellersCollectionFactory->create()
                ->setPeriod('month');
            foreach ($bestSellers as $product) {
                $productIds[] = $product->getProductId();
            }
            $collection = $this->productCollectionFactory->create()->addIdFilter($productIds);
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('*')
                ->addStoreFilter($this->getStoreId())
                ->setPageSize($selectid);
            return $collection;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    /**
     * Get the add-to-wishlist params for the given product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToWishlistParams($product)
    {
        return $this->wishlistHelper->getAddParams($product);
    }

    /**
     * Get the add-to-compare URL
     *
     * @return string
     */
    public function getAddToCompareUrl()
    {
        return $this->compareProduct->getAddUrl();
    }

    /**
     * Get the rendered product price html
     *
     * @param ModelProduct $product
     * @return string
     */
    public function getProductPrice(ModelProduct $product)
    {
        $this->ensurePriceRenderer();

        try {
            return parent::getProductPrice($product);
        } catch (\Throwable $e) {
            $this->_logger->critical($e);
        }

        return '';
    }

    /**
     * Ensure the default price render block exists in the layout
     *
     * @return void
     */
    private function ensurePriceRenderer()
    {
        if ($this->getLayout()->getBlock('product.price.render.default')) {
            return;
        }

        $this->getLayout()->createBlock(
            \Magento\Framework\Pricing\Render::class,
            'product.price.render.default',
            [
                'data' => [
                    'price_render_handle' => 'catalog_product_prices',
                    'use_link_for_as_low_as' => true,
                ],
            ]
        );
    }

    /**
     * Get the rendered attribute html, applying escaping and template directives as needed
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeHtml
     * @param string $attributeName
     * @return string|false
     */
    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        try {
            $attribute = $this->eavConfig->getAttribute(ModelProduct::ENTITY, $attributeName);
            if ($attribute &&
                $attribute->getId() &&
                $attribute->getFrontendInput() !== 'media_image' &&
                (!$attribute->getIsHtmlAllowedOnFront() &&
                !$attribute->getIsWysiwygEnabled())
            ) {
                if ($attribute->getFrontendInput() !== 'price') {
                    $attributeHtml = $this->escaper->escapeHtml($attributeHtml);
                }
                if ($attribute->getFrontendInput() === 'textarea') {
                    $attributeHtml = nl2br($attributeHtml);
                }
            }
            if ($attributeHtml !== null
                && $attribute->getIsHtmlAllowedOnFront()
                && $attribute->getIsWysiwygEnabled()
                && $this->isDirectivesExists((string)$attributeHtml)
            ) {
                $attributeHtml = $this->getTemplateProcessor()->filter($attributeHtml);
            }
            return $attributeHtml;
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }

    /**
     * Get Image URL
     *
     * @param \Magento\Catalog\Model\Product $_product
     * @return string
     */
    public function getImageUrl($_product)
    {
        $productImage = $this->helperData->init(
            $_product,
            'image'
        )->setImageFile(
            $_product->getImage()
        );
        $productImageUrl = $productImage->getUrl();
        return $productImageUrl;
    }

    /**
     * Get Config Value
     *
     * @return bool
     */
    public function getImageSwitcherConfigValue()
    {
        $value = $this->scopeConfig->getValue(
            self::ISENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getStoreId()
        );
        return $value;
    }

    /**
     * Get product label
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductlabel($product)
    {
        return $this->productLabelViewModel->getProductlabel($product);
    }

    /**
     * Get store config value
     *
     * @param string $value
     * @return array
     */
    public function getScopeconfig($value)
    {
        return $this->productLabelViewModel->getScopeconfig($value);
    }

    /**
     * Get the authentication popup config value
     *
     * @return mixed
     */
    public function getAuthenticationpopup()
    {
        return $this->authenticationviewmodel->getScopeconfig();
    }
}
