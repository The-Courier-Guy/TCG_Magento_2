<?php

namespace AppInlet\TheCourierGuy\Model\Carrier;

use AppInlet\TheCourierGuy\Helper\Shiplogic;
use AppInlet\TheCourierGuy\Observer\TCGQuote;
use AppInlet\TheCourierGuy\Plugin\ApiPlug;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use AppInlet\TheCourierGuy\Helper\Data as Helper;
use Psr\Log\LoggerInterface;
use stdClass;

class ShipmentProcessor
{
    /**
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentTrackInterfaceFactory $trackFactory
     * @param Helper $helper
     * @param Shiplogic $shipLogic
     * @param TCGQuote $tcgQuote
     * @param ApiPlug $apiPlug
     * @param Filesystem $filesystem
     * @param DirectoryList $directoryList
     * @param ShipmentSender $shipmentSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private ShipmentTrackInterfaceFactory $trackFactory,
        private Helper $helper,
        private Shiplogic $shipLogic,
        private TCGQuote $tcgQuote,
        private ApiPlug $apiPlug,
        private Filesystem $filesystem,
        private DirectoryList $directoryList,
        private ShipmentSender $shipmentSender,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param $shiplogicApi
     * @param $shipmentId
     *
     * @return string
     */
    public function getWaybillLink($shiplogicApi, $shipmentId): string
    {
        $url = '';
        try {
            $url = $shiplogicApi->getShipmentLabel($shipmentId);
            $url = json_decode((string)$url)->url;
        } catch (\Exception $exception) {
            $url = $exception->getMessage();
        }

        return $url;
    }

    /**
     * @param $shipmentId
     * @param $waybillNumber
     *
     * @return void
     */
    public function addCustomTrack($shipmentId, $waybillNumber): void
    {
        $number  = $waybillNumber;
        $carrier = 'The Courier Guy';
        $title   = 'The Courier Guy';

        try {
            $shipment = $this->shipmentRepository->get($shipmentId);
            $track    = $this->trackFactory->create()->setNumber(
                $number
            )->setCarrierCode(
                $carrier
            )->setTitle(
                $title
            );
            $shipment->addTrack($track);
            $this->shipmentRepository->save($shipment);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $order
     * @param $body
     * @param bool $returnShipment
     *
     * @return stdClass
     */
    public function createShipmentBody($order, $body, bool $returnShipment = false): stdClass
    {
        $shippingAddress = $order->getShippingAddress();

        $telephone = $this->helper->getConfig('shop_mobile');
        $email     = $this->helper->getConfig('shop_email');

        $createShipmentBody = new stdClass();

        $collection_address                = $this->shipLogic->getAddressDetail($body['sender']);
        $collection_contact                = new stdClass();
        $collection_contact->name          = $body['sender']['company'];
        $collection_contact->mobile_number = $telephone;
        $collection_contact->email         = $email;

        $createShipmentBody->collection_contact = $collection_contact;
        $createShipmentBody->collection_address = $collection_address;

        $delivery_address                = $this->shipLogic->getAddressDetail($body['receiver']);
        $delivery_contact                = new stdClass();
        $delivery_contact->name          = $order->getCustomerName();
        $delivery_contact->mobile_number = $shippingAddress->getTelephone();
        $delivery_contact->email         = $shippingAddress->getEmail();

        $createShipmentBody->delivery_contact = $delivery_contact;
        $createShipmentBody->delivery_address = $delivery_address;

        $createShipmentBody->parcels = $body['parcels'];


        $createShipmentBody->special_instructions_collection = '';
        $createShipmentBody->special_instructions_delivery   = '';
        $createShipmentBody->declared_value                  = $body['declared_value'];
        $createShipmentBody->liability_cover                 = ($this->helper->isInsuranceEnabled() && $body['declared_value'] > 0) ? 'Y' : 'N';
        $createShipmentBody->service_level_code              = $body['service_level_code'];
        $createShipmentBody->customer_reference              = $order->getIncrementId();
        if ($returnShipment) {
            $createShipmentBody->delivery_contact = $collection_contact;
            $createShipmentBody->delivery_address = $collection_address;

            $createShipmentBody->collection_contact = $delivery_contact;
            $createShipmentBody->collection_address = $delivery_address;
        }

        return $createShipmentBody;
    }

    /**
     * @throws GuzzleException|FileSystemException
     */
    public function buildShipment($order, $observerShipment): void
    {
        $shippingMethod = $order->getShippingMethod();

        $shippingMethodCode = explode('appinlet_the_courier_guy_', $shippingMethod)[1] ?? null;

        $quote_data = $this->tcgQuote->prepareQuote($order);
        $quoteId    = $order->getQuoteId();

        $requestDestinationDetails = $quote_data['requestDestinationDetails'];
        $productData               = $quote_data['productData'];
        $quote                     = $quote_data['quote'];
        $orderIncrementId          = $quote_data['orderIncrementId'];

        $shipLogicApi = $this->shipLogic;

        $declaredValue = 0.0;
        if ($this->helper->isInsuranceEnabled()) {
            foreach ($order->getAllVisibleItems() as $item) {
                $declaredValue += $item->getQtyOrdered() * $item->getPrice();
            }
            $declaredValue = round($declaredValue, 2);
        }
        $body = $this->apiPlug->prepare_api_data(
            $requestDestinationDetails,
            $productData,
            $quote,
            $orderIncrementId,
            ['declared_value' => (float)$declaredValue]
        );

        $getRatesBody = [
            'sender'              => $body['sender'],
            'receiver'            => $body['receiver'],
            'declared_value'      => $body['declared_value'],
            'collection_min_date' => $body['collection_min_date'],
            'delivery_min_date'   => $body['delivery_min_date'],
        ];
        $parcels      = [];
        foreach ($body['parcels'] as $index => $parcel) {
            $par       = [
                'key'      => $index,
                'name'     => $parcel->submitted_description,
                'quantity' => 1,
                'weight'   => $parcel->submitted_weight_kg,
                'length'   => $parcel->submitted_length_cm,
                'width'    => $parcel->submitted_width_cm,
                'height'   => $parcel->submitted_height_cm,
            ];
            $parcels[] = $par;
        }
        $getRatesBody['parcels']  = $parcels;
        $getRatesBody['boxSizes'] = $this->apiPlug->gatherBoxSizes();
        try {
            $updatedRates = $this->shipLogic->getRates($getRatesBody);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $updatedRates = ['rates' => []];
        }

        if (empty($updatedRates['rates'])) {
            $this->logger->warning('No updated rates retrieved. Using original shipping method code.');
        }
        
        $updatedRatesCodes = [];
        foreach (($updatedRates['rates'] ?? []) as $rate) {
            $updatedRatesCodes[] = $rate['service_level']['code'];
        }

        if (!empty($updatedRatesCodes) && !in_array($shippingMethodCode, $updatedRatesCodes, true)) {
            $shippingMethodCode = $updatedRatesCodes[0];
        }

        $body['service_level_code'] = $shippingMethodCode;
        $request_body               = $this->createShipmentBody($order, $body, $observerShipment === null);
        $response                   = $shipLogicApi->createShipment($request_body);
        $response                   = json_decode($response);

        $shipmentId        = $response->id;
        $trackingReference = $response->short_tracking_reference;
        $order->addCommentToStatusHistory("TCG Shipment ID: $shipmentId");
        $order->addCommentToStatusHistory("TCG Tracking Reference: $trackingReference");

        $media = $this->filesystem->getDirectoryWrite($this->directoryList::MEDIA);

        $fileName = "appinlet_the_courier_guy/" . $quoteId . ".pdf";

        $media->writeFile($fileName, base64_decode($shipmentId));

        if ($observerShipment) {
            $this->addCustomTrack($observerShipment->getId(), $trackingReference);
            $waybillUrl = $this->getWaybillLink($shipLogicApi, $shipmentId);
            $order->addCommentToStatusHistory("<a href='$waybillUrl' download >Link to waybill</a>");

            try {
                if (!$observerShipment->getEmailSent()) {
                    $this->shipmentSender->send($observerShipment);
                    $observerShipment->setEmailSent(true);
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $order->save();
    }
}
