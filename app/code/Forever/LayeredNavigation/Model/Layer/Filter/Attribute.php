<?php

declare(strict_types=1);

namespace Forever\LayeredNavigation\Model\Layer\Filter;

use Magento\CatalogSearch\Model\Layer\Filter\Attribute as AbstractFilter;
use Magento\Framework\App\RequestInterface;

class Attribute extends AbstractFilter
{
    public function apply(RequestInterface $request)
    {
        $requestValue = $request->getParam($this->_requestVar);
        $values = $this->normalizeValues($requestValue);
        if (!$values) {
            return $this;
        }

        $attribute = $this->getAttributeModel();
        $filterValues = array_map(function ($value) use ($attribute) {
            return $this->convertAttributeValue($attribute, $value);
        }, $values);

        $this->getLayer()
            ->getProductCollection()
            ->addFieldToFilter(
                $attribute->getAttributeCode(),
                count($filterValues) === 1 ? reset($filterValues) : $filterValues
            );

        foreach ($values as $value) {
            $label = $this->getOptionText($value);
            if ($label === false || $label === '') {
                continue;
            }

            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem(is_array($label) ? implode(', ', $label) : $label, $value));
        }

        $this->setItems([]);

        return $this;
    }

    private function normalizeValues($requestValue): array
    {
        if (is_array($requestValue)) {
            $values = $requestValue;
        } else {
            $values = explode(',', (string)$requestValue);
        }

        $values = array_map('trim', $values);
        $values = array_filter($values, static function ($value) {
            return $value !== '';
        });

        return array_values(array_unique($values));
    }

    private function convertAttributeValue($attribute, $value)
    {
        if ($attribute->getBackendType() === 'int') {
            return (int)$value;
        }

        return $value;
    }
}
