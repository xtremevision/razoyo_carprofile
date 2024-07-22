<?php
namespace Razoyo\CarProfile\Model\ResourceModel;

class Car extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	
	protected function _construct()
	{
		$this->_init('customer_car', 'entity_id');
	}
	
}