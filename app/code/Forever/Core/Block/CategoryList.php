<?php

namespace Forever\Core\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

class CategoryList extends \Magento\Framework\View\Element\Template
{
    public const ISENABLE = 'themedesign/imageswitcher/enable';
    public const XML_PATH_NEW_ARRIVAL = 'forever_categories/general/enabled';
    public const CATEGORIES_SELECT = 'forever_categories/home_page/category_select';
    public const XML_PATH_CART = 'checkout/cart/redirect_to_cart';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Forever\Core\Model\Config\Category
     */
    protected $category;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $helperData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Forever\Productlabel\ViewModel\ProductLabelViewModel
     */
    protected $productLabelViewModel;

    /**
     * @var \Magento\Catalog\Pricing\Price\SpecialPriceBulkResolverInterface
     */
    protected $specialPriceBulkResolver;

    /**
     * @var array
     */
    protected $specialPriceMap = [];

    /**
     * @var ListProduct
     */
    protected $listProductBlock;

    /**
     * @var \Magento\Catalog\Helper\Product\Compare
     */
    protected $compareProduct;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistHelper;

    /**
     * @var \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel
     */
    protected $authenticationviewmodel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Block\Product\Context $gridcontext
     * @param ListProduct $listProductBlock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Forever\Core\Model\Config\Category $category
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Helper\Image $helperData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel
     * @param \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Block\Product\Context $gridcontext,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Forever\Core\Model\Config\Category $category,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Helper\Image $helperData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Forever\Productlabel\ViewModel\ProductLabelViewModel $productLabelViewModel,
        \Forever\AuthenticationPopUp\ViewModel\AuthenticationViewModel $authenticationviewmodel,
        array $data = []
    ) {
        $this->listProductBlock = $listProductBlock;
        $this->scopeConfig = $scopeConfig;
        $this->category = $category;
        $this->compareProduct = $gridcontext->getCompareProduct();
        $this->wishlistHelper = $gridcontext->getWishlistHelper();
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->productLabelViewModel = $productLabelViewModel;
        $this->authenticationviewmodel = $authenticationviewmodel;
        parent::__construct($context, $data);
    }

    /**
     * Check if the module has been enabled in the admin
     *
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NEW_ARRIVAL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the category factory instance
     *
     * @return \Magento\Catalog\Model\CategoryFactory
     */
    public function getCategoryFactory()
    {
        return $this->categoryFactory;
    }

    /**
     * Check whether the cart redirect config is enabled
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CART,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the add-to-cart post params for the given product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
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
     * Get the selected category
     *
     * @return array|null
     */
    public function getSelectedCategory()
    {
        return $this->category->getSelected(
            $this->_scopeConfig->getValue(
                self::CATEGORIES_SELECT,
                ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * Return detail of products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    /**
     * Return detail renderer
     *
     * @param string|null $type
     * @return mixed
     */
    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }

    /**
     * Get the details renderer list block
     *
     * @return mixed
     */
    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'homepage.toprenderers'
        );
    }

    /**
     * Get the rendered product price html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string|null $priceType
     * @return string
     */
    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
    ) {
        $priceType = $priceType ?: \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE;
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $priceRender->setData('is_product_list', true);
            $priceRender->setData('special_price_map', $this->getSpecialPriceMap($product));
            $price = $priceRender->render(
                $priceType,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true,
                ]
            );
        }
        return $price;
    }

    /**
     * Get the products of the given category
     *
     * @param int $categoryId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getCategoryProducts($categoryId)
    {
        $ids = $this->getSelectedCategoryIds();
        $products = clone $this->getProductCollection();
        $products->addCategoriesFilter(['eq' => $categoryId]);
        $products->addAttributeToSort('created_at', 'DESC');
        if (!$products->getAllIds()) {
            $this->specialPriceMap = [];
            return $products;
        }
        /*
        $this->specialPriceMap = $this->getSpecialPriceBulkResolver()->generateSpecialPriceMap(
            (int) $this->storeManager->getStore()->getId(),
            $products
        );
        */
        return $products;
    }

    /**
     * Get the special price map for the given product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getSpecialPriceMap(\Magento\Catalog\Model\Product $product)
    {
        if (!isset($this->specialPriceMap[$product->getId()])) {
            $this->specialPriceMap[$product->getId()] = (bool) ($product->getFinalPrice() < $product->getPrice());
        }

        return $this->specialPriceMap;
    }

    /**
     * Get the special price bulk resolver instance
     *
     * @return \Magento\Catalog\Pricing\Price\SpecialPriceBulkResolverInterface
     */
    protected function getSpecialPriceBulkResolver()
    {
        if ($this->specialPriceBulkResolver === null) {
            $this->specialPriceBulkResolver = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Catalog\Pricing\Price\SpecialPriceBulkResolverInterface::class
            );
        }

        return $this->specialPriceBulkResolver;
    }

    /**
     * Get the visible, enabled product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        if (!$this->productCollection) {
            $productCollection = $this->productCollectionFactory->create();
            $productCollection->addAttributeToSelect('*');
            $productCollection->addAttributeToFilter(
                'visibility',
                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
            );
            $productCollection->addAttributeToFilter(
                'status',
                \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
            );
            $this->productCollection = $productCollection;
        }
        return $this->productCollection;
    }

    /**
     * Get the selected category ids
     *
     * @return array
     */
    public function getSelectedCategoryIds()
    {
        return $this->category->getSelectedCategoryByIds(
            $this->_scopeConfig->getValue(
                self::CATEGORIES_SELECT,
                ScopeInterface::SCOPE_STORE
            )
        );
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
