<?php

declare(strict_types=1);

namespace Forever\Faq\Block\Adminhtml\Question\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Provides the save button for an FAQ question.
 */
class SaveButton implements ButtonProviderInterface
{
    /**
     * Get button data.
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label'          => __('Save'),
            'class'          => 'primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
