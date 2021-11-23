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
    public function afterGetAllowedUrls(Tfa $subject, $result)
    {
        if (is_array($result)) {
            $result = array_merge($result, ['tfa_rtfa_requestprovider']);
        }
        return $result;
    }
}
