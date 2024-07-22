<?php
namespace Razoyo\CarProfile\Controller\Index;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Razoyo\CarProfile\Helper\Data as CarHelper;

class SaveCar extends \Razoyo\CarProfile\Controller\AbstractAccount implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
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
    protected $carFactory;
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
        \Razoyo\CarProfile\Model\CarFactory $carFactory,
        \Magento\Customer\Model\Session $customerSession,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->carHelper = $carHelper;
        $this->logger = $logger;
        $this->carFactory = $carFactory;
        $this->customerSession = $customerSession;
        $this->request = $request;
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
                $car = $this->request->getParam('car');
            
                if(!empty($car))
                {
                    $customerId = $this->customerSession->getCustomerId();
                    $carObject = $this->carFactory->create()->load($customerId, 'customer_id');
                    if(!$carObject->getId())
                    {
                        $carObject->setCustomerId($customerId);
                    }
                    else
                    {
                        $carObject->setUpdatedAt(time());
                    }
    
                    $carObject->setCarId($car['id']);
                    $carObject->setImage($car['image']);
                    $carObject->setMake($car['make']);
                    $carObject->setModel($car['model']);
                    $carObject->setYear($car['year']);
                    $carObject->setMpg($car['mpg']);
                    $carObject->setSeats($car['seats']);
                    $carObject->setPrice($car['price']);
                    $carObject->save();
                }
    
                $resultJson->setData([
                    'success' => true
                ]);    
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $resultJson; 
    }
}