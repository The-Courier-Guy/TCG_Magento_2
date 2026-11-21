<?php

namespace AppInlet\TheCourierGuy\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class HttpsUrl extends Value
{
    public function beforeSave()
    {
        $url = trim((string)$this->getValue());
        $this->setValue($url);

        if ($url !== '' &&
            !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new LocalizedException(__('Please enter a valid URL.'));
        }

        if (parse_url($url, PHP_URL_SCHEME) !== 'https') {
            throw new LocalizedException(__('The URL must use HTTPS.'));
        }

        return parent::beforeSave();
    }
}