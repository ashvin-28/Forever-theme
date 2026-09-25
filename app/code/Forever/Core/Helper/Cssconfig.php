<?php

namespace Forever\Core\Helper;

class Cssconfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $generatedCssFolder;

    /**
     * @var string
     */
    protected $generatedCssPath;

    /**
     * @var string
     */
    protected $generatedCssDir;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $filesystemDriver;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DriverInterface $filesystemDriver
    ) {
        $base = BP;
        $this->storeManager = $storeManager;
        $this->generatedCssFolder = 'forever/configed_css/';
        $this->generatedCssPath = 'pub/media/' . $this->generatedCssFolder;
        $this->generatedCssDir = $base . '/' . $this->generatedCssPath;
        $this->filesystemDriver = $filesystemDriver;

        parent::__construct($context);
    }

    /**
     * Get the current store's base media URL
     *
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get the generated CSS directory path
     *
     * @return string
     */
    public function getCssConfigDir()
    {
        return $this->generatedCssDir;
    }

    /**
     * Get the current store's generated design CSS file URL
     *
     * @return string
     */
    public function getDesignFile()
    {
        $code = $this->storeManager->getStore()->getCode();
        $fileName = 'css_' . $code . '.css';
        $fileUrl = $this->getBaseMediaUrl() . $this->generatedCssFolder . $fileName;
        $filePath = $this->generatedCssDir . $fileName;

        if ($this->filesystemDriver->isFile($filePath)) {
            $fileUrl .= '?v=' . $this->filesystemDriver->stat($filePath)['mtime'];
        }

        return $fileUrl;
    }

    /**
     * Get the Forever theme web assets media directory
     *
     * @return string
     */
    public function getPortoWebDir()
    {
        return $this->getBaseMediaUrl() . 'forever/web/';
    }

    /**
     * Get the dynamic CSS link tag markup
     *
     * @return string
     */
    public function getDynamicCssLink()
    {
        return '<link rel="stylesheet" type="text/css" media="all" href="' . $this->getDesignFile() . '"/>';
    }
}
