<?php

namespace AppInlet\TheCourierGuy\Model\ResourceModel\Shipment;

use AppInlet\TheCourierGuy\Model\ResourceModel\Shipment;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';


    protected function _construct(): void // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(
            \AppInlet\TheCourierGuy\Model\Shipment::class,
            Shipment::class
        );
    }
}
