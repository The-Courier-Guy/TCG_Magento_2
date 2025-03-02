<?php

namespace AppInlet\TheCourierGuy\Observer;

use AppInlet\TheCourierGuy\Helper\Data as Helper;
use AppInlet\TheCourierGuy\Logger\Logger as Monolog;
use AppInlet\TheCourierGuy\Model\ShipmentFactory;
use AppInlet\TheCourierGuy\Plugin\ApiPlug;
use Magento\Quote\Model\QuoteFactory;

class TCGQuote
{

    private ApiPlug $apiPlug;
    private Monolog $monolog;
    private Helper $helper;
    private QuoteFactory $quoteFactory;
    private ShipmentFactory $shipmentFactory;

    public function __construct(
        ShipmentFactory $shipmentFactory,
        ApiPlug $apiPlug,
        Monolog $monolog,
        Helper $helper,
        QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory    = $quoteFactory;
        $this->helper          = $helper;
        $this->shipmentFactory = $shipmentFactory;
        $this->apiPlug         = $apiPlug;
        $this->monolog         = $monolog;
    }

    /**
     * @param $order
     *
     * @return array
     */
    public function prepareQuote($order): array
    {
        $quoteId          = $order->getQuoteId();
        $orderIncrementId = $order->getIncrementId();
        $quote            = $this->quoteFactory->create()->load($quoteId);
        $result           = [];

        $shippingMethod = $order->getShippingMethod();
        $this->monolog->info('In prepareQuote: Shipping method: ' . $shippingMethod);

        if (strpos($shippingMethod, 'appinlet_the_courier_guy_') === 0) {
            //start of loop through items to create array version of items
            $productData = [];

            $packageItemId = 0;

            foreach ($order->getAllItems() as $key => $item) {
                // Skip virtual products
                if ($item->getIsVirtual()) {
                    continue;
                }

                $lineItem             = [];
                $lineItem['key']      = $packageItemId;
                $lineItem['name']     = $item->getName();
                $lineItem['quantity'] = $item->getQtyOrdered();
                $lineItem['weight']   = $item->getQtyOrdered() * $item->getWeight();

                $lineItem['length'] = $item->getLength();
                $lineItem['width']  = $item->getWidth();
                $lineItem['height'] = $item->getHeight();

                if ($item->getLength() == "") {
                    $lineItem['length'] = $this->helper->getConfig('typicallength'); /*if not set*/
                }

                if ($item->getWidth() == "") {
                    $lineItem['width'] = $this->helper->getConfig('typicalwidth'); /*if not set*/
                }

                if ($item->getHeight() == "") {
                    $lineItem['height'] = $this->helper->getConfig('typicalheight'); /*if not set*/
                }

                if ($item->getWeight() == "") {
                    $lineItem['weight'] = $item->getQty() * $this->helper->getConfig('typicalweight'); /*if not set*/
                }

                array_push($productData, $lineItem);

                $packageItemId = $packageItemId + 1;
            }

            //create array of destination details

            $requestDestinationDetails = [
                "street"      => $order->getShippingAddress()->getData("street"),
                "city"        => $order->getShippingAddress()->getData("city"),
                "postal_code" => $order->getShippingAddress()->getData("postcode")
            ];

            $result = [
                'requestDestinationDetails' => $requestDestinationDetails,
                'productData'               => $productData,
                'quote'                     => $quote,
                'orderIncrementId'          => $orderIncrementId,
            ];
        }

        return $result;
    }

    public function createQuote($order)
    {
        $quote_data = $this->prepareQuote($order);

        $requestDestinationDetails = $quote_data['requestDestinationDetails'];
        $productData               = $quote_data['productData'];
        $quote                     = $quote_data['quote'];
        $orderIncrementId          = $quote_data['orderIncrementId'];

        return $this->apiPlug->getQuote(
            $requestDestinationDetails,
            $productData,
            $quote,
            $orderIncrementId
        );
    }
}
