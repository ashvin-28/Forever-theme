<?php

declare(strict_types=1);

namespace Forever\Dailydeals\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Information extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Forever_Dailydeals::system/config/info.phtml';

    /**
     * Remove scope checkboxes and render field
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return rendered HTML for the element
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }
}
