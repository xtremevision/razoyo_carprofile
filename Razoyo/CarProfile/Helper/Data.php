<?php
namespace Razoyo\CarProfile\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\HTTP\Client\Curl;
use \Magento\Customer\Model\Session;
use \Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Data extends AbstractHelper
{
    /**
    * @var \Magento\Framework\HTTP\Client\Curl
    */

    protected $curl;
    protected $customerSession;
    protected $priceHelper;

    /**
     * @var \Magento\Framework\App\Helper\Context
    */

   protected $userContext;

    /**
    * constructor.
    * @param \Magento\Framework\HTTP\Client\Curl $curl
    */
    public function __construct(
        Curl $curl,
        Session $customerSession,
        PriceHelper $priceHelper,
        \Magento\Authorization\Model\UserContextInterface $userContext
    ) {
        $this->curl = $curl;
        $this->customerSession = $customerSession;
        $this->priceHelper = $priceHelper;
        $this->userContext = $userContext;        
    }

    public function getCustomerId()
    {
      return $this->userContext->getUserId();
    }
    
    public function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getCars()
    {
        $this->curl->get("https://exam.razoyo.com/api/cars");
        $headers = $this->curl->getHeaders();

        if(isset($headers['your-token']))
        {
            $token = $headers['your-token'];
            $this->customerSession->setAuthToken($token);
        }

        $result = $this->curl->getBody();
        $decoded = json_decode($result);
        if(isset($decoded->expires))
        {
            $this->customerSession->setCarsExpiry($decoded->expires);
        }

        return $decoded;
    }

    public function getCarData($carId)
    {
        try
        {
            $expires = $this->customerSession->getCarsExpiry();
            if(time() > $expires)
            {
                $this->getCars();
            }

            $token = $this->customerSession->getAuthToken();

            $authorization = "Bearer " . trim($token); // Prepare the authorisation token
            $url = sprintf("https://exam.razoyo.com/api/cars/%s", $carId);
            $this->curl->addHeader("Content-Type", "application/json");
            $this->curl->addHeader("Authorization", $authorization);
            
            $this->curl->get($url);
            $result = $this->curl->getBody();           
            $decoded = json_decode($result);
            if(isset($decoded->car))
            {
                $decoded->car->formattedPrice = $this->getFormattedPrice($decoded->car->price);
            }
            return $decoded;
        }
        catch(\Exception $e)
        {
            //var_dump($e->getMessage());
        }
    }
}