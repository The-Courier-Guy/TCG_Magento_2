<?php

namespace AppInlet\TheCourierGuy\Plugin\Carrier;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class Places
{
    protected ShippingMethodExtensionFactory $extensionFactory;

    /**
     * Description constructor.
     *
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(
        ShippingMethodExtensionFactory $extensionFactory
    ) {
        $this->extensionFactory = $extensionFactory;
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
        $extensionAttribute = $result->getExtensionAttributes() ?
            $result->getExtensionAttributes()
            :
            $this->extensionFactory->create();
        $extensionAttribute->setPlaces($rateModel->getPlaces());
        $result->setExtensionAttributes($extensionAttribute);

        return $result;
    }
}
