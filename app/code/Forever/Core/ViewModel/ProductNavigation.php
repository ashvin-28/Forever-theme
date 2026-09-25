<?php
namespace Forever\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Helper\Image;
use Magento\Store\Model\StoreManagerInterface;

class ProductNavigation implements ArgumentInterface
{
    public const PRODUCT_THUMBNAIL_IMAGE_ID = 'product_thumbnail_image';

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var array|null
     */
    protected ?array $nextPrevious = null;

    /**
     * @var Category
     */
    protected Category $categoryModel;

    /**
     * @var Image
     */
    protected Image $imageHelper;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @var mixed
     */
    protected $productCollectionResource = null;

    /**
     * @param LoggerInterface $logger
     * @param Category $categoryModel
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     */
    public function __construct(
        LoggerInterface $logger,
        Category $categoryModel,
        StoreManagerInterface $storeManager,
        Image $imageHelper
    ) {
        $this->logger = $logger;
        $this->categoryModel = $categoryModel;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get the product collection of the given category, indexed by product id
     *
     * @param Category $category
     * @return array|null
     */
    public function getCategoryProductIds($category)
    {
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('*');
        $this->productCollectionResource = $categoryProducts;
        foreach ($categoryProducts as $product) {
            $this->nextPrevious[$product->getId()] = $product;
        }
        return $this->nextPrevious;
    }

    /**
     * Get the current active category the given product belongs to
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return Category|null
     */
    public function getCurrentCategory($product)
    {
        $currentCategory = $product->getCategory();
        if (!$currentCategory || $currentCategory->getIsActive() == 0) {
            foreach ($product->getCategoryCollection() as $category) {
                $categoryId = $category->getId();
                $currentCategory = $this->categoryModel->load($categoryId);
                if ($currentCategory->getIsActive()) {
                    return $currentCategory;
                }
            }
            return null;
        }
        return $currentCategory;
    }

    /**
     * Get the previous and next products relative to the given product within its category
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array|null
     */
    public function getPreviousAndNext($product)
    {
        if (!$this->nextPrevious) {
            $currentCategory = $this->getCurrentCategory($product);
            if (!$currentCategory) {
                return null;
            }
            $this->nextPrevious = $this->getCategoryProductIds($currentCategory);
        }
        $productId = $product->getId();
        $nextPrevious = $this->nextPrevious;
        if (!$nextPrevious) {
            return null;
        }
        $prevProduct = '';
        foreach ($nextPrevious as $id => $product) {
            if ($id == $productId) {
                break;
            }
            $prevProduct = $product;
            next($nextPrevious);
        }
        return [$prevProduct, next($nextPrevious)];
    }

    /**
     * Get the previous product relative to the given product within its category
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed|string
     */
    public function getPrevProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        return $previousAndNext ? current($previousAndNext) : '';
    }

    /**
     * Get the next product relative to the given product within its category
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed|string
     */
    public function getNextProduct($product)
    {
        $previousAndNext = $this->getPreviousAndNext($product);
        return $previousAndNext ? next($previousAndNext) : '';
    }

    /**
     * Get the current store's base media URL
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get the product thumbnail image URL
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string|false
     */
    public function getProductThumbnail($product)
    {
        try {
            return $this->imageHelper
                ->init($product, self::PRODUCT_THUMBNAIL_IMAGE_ID)
                ->getUrl();
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }
        return false;
    }
}
