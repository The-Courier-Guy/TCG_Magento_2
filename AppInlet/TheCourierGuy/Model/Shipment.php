<?php

namespace AppInlet\TheCourierGuy\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Shipment extends AbstractModel
{
    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Shipment::class);
    }
}
