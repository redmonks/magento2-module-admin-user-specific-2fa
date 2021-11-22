<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Plugin\TwoFactorAuth;

use Magento\TwoFactorAuth\Model\Tfa;

class ModelTfa
{
    public function afterGetUserProviders(Tfa $subject, $result, int $userId)
    {
        $defaultUserProviderCode = $subject->getDefaultProviderCode($userId);
        if ($defaultUserProviderCode) {
            return [$subject->getProvider($defaultUserProviderCode)];
        }
        return $subject->getForcedProviders();
    }

    public function afterGetProvidersToActivate(Tfa $subject, $result, int $userId)
    {
        $providers = $subject->getUserProviders($userId);
        // check if user has default provider
        $defaultUserProviderCode = $subject->getDefaultProviderCode($userId);
        if ($defaultUserProviderCode) {
            $providers = [$subject->getProvider($defaultUserProviderCode)];
        }

        $res = [];
        foreach ($providers as $provider) {
            if (!$provider->isActive($userId)) {
                $res[] = $provider;
            }
        }
        return $res;
    }
}
