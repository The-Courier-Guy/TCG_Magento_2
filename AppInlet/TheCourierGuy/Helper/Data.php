<?php

namespace AppInlet\TheCourierGuy\Helper;

use Exception;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use AppInlet\TheCourierGuy\Plugin\ShipLogicApiPayload;

class Data extends AbstractHelper
{
    public const string XML_PATH_CATALOG = 'carriers/';

    protected WriterInterface $configWriter;
    protected LoggerInterface $logger;
    protected TypeListInterface $cacheTypeList;
    protected Pool $cacheFrontendPool;
    protected ShipLogicApiPayload $shipLogicApiPayload;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param WriterInterface $configWriter
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     * @param LoggerInterface $logger
     * @param ShipLogicApiPayload $shipLogicApiPayload
     */
    public function __construct(
        Context $context,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool,
        LoggerInterface $logger,
        ShipLogicApiPayload $shipLogicApiPayload,
    ) {
        $this->configWriter        = $configWriter;
        $this->cacheTypeList       = $cacheTypeList;
        $this->cacheFrontendPool   = $cacheFrontendPool;
        $this->logger              = $logger;
        $this->shipLogicApiPayload = $shipLogicApiPayload;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @param null $storeId
     *
     * @return string
     */
    public function getConfigValue($field, $storeId = null): string
    {
        if ($fieldValue = $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )
        ) {
            return $fieldValue;
        }

        return "";
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return string
     */
    public function getConfig($code, $storeId = null): string
    {
        return $this->getConfigValue(self::XML_PATH_CATALOG . "appinlet_the_courier_guy/" . $code, $storeId);
    }

    /**
     * @param $code
     * @param $value
     */
    public function SetConfigData($code, $value): void
    {
        $path = self::XML_PATH_CATALOG . "appinlet_the_courier_guy/" . $code;
        try {
            $this->configWriter->save($path, $value);
            $this->flushCache();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Flush config cache
     */
    public function flushCache(): void
    {
        $_types = [
            'config'
        ];

        foreach ($_types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }

    public function isInsuranceEnabled(): bool
    {
        return $this->getConfig('enable_insurance') === '1';
    }

    public function isCustomShippingLabelsEnabled($storeId = null): bool
    {
        return $this->getConfigValue(
            'carriers/appinlet_the_courier_guy/enable_custom_labels_and_pricing',
            $storeId
        ) === '1';
    }

    /**
     * Get custom shipping label name for a specific rate code
     *
     * @param string $rateCode
     * @param null $storeId
     * @return string
     */
    public function getCustomShippingLabel(string $rateCode, $storeId = null): string
    {
        $configPath = "carriers/appinlet_the_courier_guy/courier_guy_{$rateCode}/shipping_label_name_{$rateCode}";
        return $this->getConfigValue($configPath, $storeId) ?: '';
    }

    /**
     * Get custom pricing type for a specific rate code
     *
     * @param string $rateCode
     * @param null $storeId
     * @return string
     */
    public function getCustomPricingType(string $rateCode, $storeId = null): string
    {
        $configPath = "carriers/appinlet_the_courier_guy/courier_guy_{$rateCode}/pricing_type_{$rateCode}";
        return $this->getConfigValue($configPath, $storeId) ?: '';
    }

    /**
     * Get custom rate for a specific rate code
     *
     * @param string $rateCode
     * @param null $storeId
     * @return float|int
     */
    public function getRateModifier(string $rateCode, $storeId = null): float|int
    {
        $configPath = "carriers/appinlet_the_courier_guy/courier_guy_{$rateCode}/custom_rate_{$rateCode}";
        $rate       = $this->getConfigValue($configPath, $storeId);
        return $rate ? (float)$rate : 0.0;
    }
}
