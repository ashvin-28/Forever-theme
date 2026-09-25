<?php

namespace Forever\Core\Model\Config;

class Category implements \Magento\Framework\Option\ArrayInterface
{

    public const CATEGORY_LEVEL = 3;
    public const ROOT_CATALOG_LABEL = 'Root Catalog';
    public const DEFAULT_CATEGORY_LABEL = 'Default Category';

    /**
     * @var \Magento\Catalog\Model\Config\Source\Category
     */
    protected $category;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param \Magento\Catalog\Model\Config\Source\Category $category
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Config\Source\Category $category,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->category = $category;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $firstLevelCategories = [];
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('id');
        $collection->addAttributeToSelect('name');
        $collection->addLevelFilter(self::CATEGORY_LEVEL);
        
        foreach ($collection as $key => $category) {
            if ($category->getName() != self::ROOT_CATALOG_LABEL &&
                $category->getName() != self::DEFAULT_CATEGORY_LABEL) {
                $firstLevelCategories[$category->getId()]['value'] = $category->getId();
                $firstLevelCategories[$category->getId()]['label'] = $category->getName();
            }
        }
        return $firstLevelCategories;
    }

    /**
     * Get the selected category labels keyed by category id
     *
     * @param string $optionIds
     * @return array|null
     */
    public function getSelected($optionIds)
    {
        if ($optionIds) {
            $categoryId = explode(',', $optionIds);
            $option = $this->toOptionArray();
            $result = [];
            foreach ($option as $key => $value) {
                if (in_array($key, $categoryId)) {
                    $valueData = $value['value'];
                    $result[$valueData] = $value['label'];
                }
            }
            return $result;
        }
    }

    /**
     * Get the selected category values keyed numerically
     *
     * @param string $optionIds
     * @return array
     */
    public function getSelectedCategoryByIds($optionIds)
    {
        $categoryId= explode(',', $optionIds);
        $option = $this->toOptionArray();
        $result = [];
        foreach ($option as $key => $value) {
            if (in_array($key, $categoryId)) {
                $result[] =$value['value'];
            }
        }
        return $result;
    }
}
