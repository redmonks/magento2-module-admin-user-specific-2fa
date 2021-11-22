<?php
namespace RedMonks\UserSpecificTwoFactorAuth\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;

class Providers implements ArgumentInterface
{
    /** @var TfaInterface */
    private $tfa;

    public function __construct(
        TfaInterface $tfa
    ) {
        $this->tfa = $tfa;
    }

    public function getAvailableProviders()
    {
        return $this->tfa->getForcedProviders()?:$this->tfa->getAllEnabledProviders();
    }
}
