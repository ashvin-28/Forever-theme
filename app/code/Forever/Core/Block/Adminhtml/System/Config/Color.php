<?php
namespace Forever\Core\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    /**
     * Render the color picker markup when the element is clicked
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();

        $value = $element->getValue() ?: '#000000';

        $html .= '
        <script>
        require([
            "jquery",
            "spectrum"
        ], function ($) {
            $("#'.$element->getHtmlId().'").spectrum({
                preferredFormat: "hex",
                showInput: true,
                allowEmpty: false,
                color: "'.$value.'",
                change: function(color) {
                    $("#'.$element->getHtmlId().'").val(color.toHexString());
                }
            });
        });
        </script>';

        return $html;
    }
}
