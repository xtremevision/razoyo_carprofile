<?php
namespace Razoyo\CarProfile\Model\ResourceModel\Car;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'entity_id';
	protected $_eventPrefix = 'customer_car_collection';
	protected $_eventObject = 'car_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Razoyo\CarProfile\Model\Car', 'Razoyo\CarProfile\Model\ResourceModel\Car');
	}

}