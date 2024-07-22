<?php
namespace Razoyo\CarProfile\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Razoyo\CarProfile\Helper\Data as CarHelper;

class GetCarInfo extends \Razoyo\CarProfile\Controller\AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    protected $carHelper;
    protected $logger;
    protected $customerSession;
    
    /**
     * @param Context                                             $context
     * @param PageFactory                                         $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        CarHelper $carHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession,
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->carHelper = $carHelper;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        try {

            if(!$this->customerSession->isLoggedIn()) {
                $response['errors'] = true;
                $response['message'] = 'Customer not logged in';
                $response['redirectUrl'] = $this->_url->getUrl('customer/account/login');
                /** @var \Magento\Framework\Controller\Result\Json $resultJson */
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
            }
            else
            {
                $carId = $this->getRequest()->getParam('carId');
                $carData = $this->carHelper->getCarData($carId);
                $resultJson->setData($carData);            
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $resultJson; 
    }
}