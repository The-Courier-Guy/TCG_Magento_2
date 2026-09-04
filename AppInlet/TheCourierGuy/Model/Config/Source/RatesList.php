<?php

namespace AppInlet\TheCourierGuy\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RatesList implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('Select Options')],
            ['value' => 'AIR', 'label' => __('The Courier Guy - AIR')],
            ['value' => 'ECO', 'label' => __('The Courier Guy - ECO')],
            ['value' => 'ECOB', 'label' => __('The Courier Guy - ECOB')],
            ['value' => 'ECOR', 'label' => __('The Courier Guy - ECOR')],
            ['value' => 'ECORB', 'label' => __('The Courier Guy - ECORB')],
            ['value' => 'INN', 'label' => __('The Courier Guy - INN')],
            ['value' => 'LLS', 'label' => __('The Courier Guy - LLS')],
            ['value' => 'LLX', 'label' => __('The Courier Guy - LLX')],
            ['value' => 'LOF', 'label' => __('The Courier Guy - LOF')],
            ['value' => 'LOX', 'label' => __('The Courier Guy - LOX')],
            ['value' => 'LSE', 'label' => __('The Courier Guy - LSE')],
            ['value' => 'LPF', 'label' => __('The Courier Guy - LPF')],
            ['value' => 'LPP', 'label' => __('The Courier Guy - LPP')],
            ['value' => 'LSF', 'label' => __('The Courier Guy - LSF')],
            ['value' => 'LSP', 'label' => __('The Courier Guy - LSP')],
            ['value' => 'OVN', 'label' => __('The Courier Guy - OVN')],
            ['value' => 'LSX', 'label' => __('The Courier Guy - LSX')],
            ['value' => 'NFS', 'label' => __('The Courier Guy - NFS')],
            ['value' => 'OVNR', 'label' => __('The Courier Guy - OVNR')],
            ['value' => 'PRI', 'label' => __('The Courier Guy - PRI')],
            ['value' => 'PRIR', 'label' => __('The Courier Guy - PRIR')],
            ['value' => 'RIN', 'label' => __('The Courier Guy - RIN')],
            ['value' => 'SDX', 'label' => __('The Courier Guy - SDX')],
            ['value' => 'SPX', 'label' => __('The Courier Guy - SPX')]
        ];
    }
}
