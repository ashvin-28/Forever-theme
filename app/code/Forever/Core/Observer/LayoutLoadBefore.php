<?php

namespace Forever\Core\Observer;

use Magento\Store\Model\ScopeInterface;

class LayoutLoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    public const XML_PATH_HEADER_STYLE = 'forever_general/header/style';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_registry = $registry;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Add the configured header style layout handle before layout generation
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $headerType = $this->getHeaderStyle();
        $layout = $observer->getLayout();
        try {
            if ($headerType) {
                $layout->getUpdate()->addHandle($headerType);
            }
        } catch (Exception $e) {
            $layout->getUpdate()->addHandle('default_header');
        }
        return $this;
    }

    /**
     * Get the configured header style value from store config
     *
     * @return string|null
     */
    public function getHeaderStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
