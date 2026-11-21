<?php

namespace AppInlet\TheCourierGuy\Plugin\Carrier;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class Places
{
    /**
     * Description constructor.
     *
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(protected ShippingMethodExtensionFactory $extensionFactory)
    {
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     *
     * @return mixed
     */
    public function afterModelToDataObject($subject, $result, $rateModel): mixed
    {
        $extensionAttribute = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttribute->setPlaces($rateModel->getPlaces());
        $result->setExtensionAttributes($extensionAttribute);

        return $result;
    }
}
