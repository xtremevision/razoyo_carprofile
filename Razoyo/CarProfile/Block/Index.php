<?php
namespace Razoyo\CarProfile\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Razoyo\CarProfile\Helper\Data as CarHelper;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $carHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    protected $carFactory;

    public function __construct(
        Context $context,
        CarHelper $carHelper,
        CustomerSession $customerSession,
        \Razoyo\CarProfile\Model\CarFactory $carFactory,
        array $data = []
    )
    {
        $this->carHelper            = $carHelper;
        $this->_customerSession     = $customerSession;
        $this->carFactory     = $carFactory;
        parent::__construct($context, $data);
    }

    public function getCars()
    {
        $cars = $this->carHelper->getCars();
        return $cars;
    }

    public function getSavedCar()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $carObject = $this->carFactory->create()->load($customerId, 'customer_id');
        return $carObject;
    }    
}