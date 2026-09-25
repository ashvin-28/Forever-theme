<?php

namespace Forever\Brand\Block\Adminhtml\Brand\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Button provider for resetting the brand form.
 */
class ResetButton implements ButtonProviderInterface
{
    /**
     * Get button configuration.
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
