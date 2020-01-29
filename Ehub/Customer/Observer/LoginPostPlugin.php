<?php

namespace Ehub\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPostPlugin implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_session = $customerSession;
        $this->_customerUrl = $customerUrl;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Check captcha on user login page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws NoSuchEntityException
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
	    $controller = $observer->getControllerAction();

	    if (!isset($_POST['vendor_login'])) {
	        $url = $this->_urlBuilder->getUrl("../../");
	        $this->_session->setBeforeAuthUrl($url);
	        $this->_session->setAfterAuthUrl($url);
	    }

        return $this;
    }
}
