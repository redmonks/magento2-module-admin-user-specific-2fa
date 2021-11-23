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
        return $result;
    }
}
