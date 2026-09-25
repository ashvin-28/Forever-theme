<?php
namespace Forever\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class SystemConfigurations implements ArgumentInterface
{
    public const XML_PATH_HEADER_STYLE = 'forever_general/header/style';
    public const XML_PATH_FOOTER_STYLE = 'forever_general/footer/style';
    public const XML_PATH_STICKY_HEADER_TYPE = 'forever_general/header/stickyheader';
    public const XML_PATH_STICKY_HEADER_TYPE_ENABLE = 'forever_general/header/sticky';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected \Psr\Log\LoggerInterface $logger;

    /**
     * @var array|null
     */
    protected ?array $nextPrevious = null;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected \Magento\Catalog\Model\Category $categoryModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected \Magento\Customer\Model\Session $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Model\Category $categoryModel
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Category $categoryModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->categoryModel = $categoryModel;
        $this->customerSession = $customerSession;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get header stype from admin configuration
     *
     * @return string
     */
    public function getHeaderStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HEADER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get footer stype from admin configuration
     *
     * @return string
     */
    public function getfooterStyle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FOOTER_STYLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get a store config value for the given path
     *
     * @param string $path
     * @return mixed
     */
    public function getconfigValue($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether the current customer is logged in
     *
     * @return bool
     */
    public function getCustomerLogin()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * Get sticky header type from system configuration
     *
     * @return string
     */
    public function getStickyHeaderType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STICKY_HEADER_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get sticky header type enable from system configuration
     *
     * @return integer
     */
    public function isStickyEnable()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_STICKY_HEADER_TYPE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
