<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Model\Sources;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;

class Provider implements OptionSourceInterface
{
    /** @var TfaInterface */
    private $tfa;

    protected $availableProviders = [];

    public function __construct(
        TfaInterface $tfa
    ) {
        $this->tfa = $tfa;
    }

    public function toOptionArray()
    {
        if (!$this->availableProviders) {
            $this->availableProviders[] = ['label' => '--- Choose provider ---', 'value' => ''];
            $providers = $this->tfa->getForcedProviders();
            if (is_array($providers) && count($providers) > 0) {
                foreach ($providers as $provider) {
                    $this->availableProviders[] = ['label' => $provider->getName(), 'value' => $provider->getCode()];
                }
            }
        }
        return $this->availableProviders;
    }
}
