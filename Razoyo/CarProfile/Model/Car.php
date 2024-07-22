<?php
namespace Razoyo\CarProfile\Model;

class Car extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'customer_car';

	protected $_cacheTag = 'customer_car';

	protected $_eventPrefix = 'customer_car';

	protected function _construct()
	{
		$this->_init('Razoyo\CarProfile\Model\ResourceModel\Car');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}