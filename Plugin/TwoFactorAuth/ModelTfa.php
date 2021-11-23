<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Plugin\TwoFactorAuth;

use Magento\TwoFactorAuth\Model\Tfa;

class ModelTfa
{
    public function afterGetUserProviders(Tfa $subject, $result, int $userId)
    {
        if ($defaultUserProviderCode = $subject->getDefaultProviderCode($userId)) {
            return [$subject->getProvider($defaultUserProviderCode)];
        }
        return $result;
    }
}
