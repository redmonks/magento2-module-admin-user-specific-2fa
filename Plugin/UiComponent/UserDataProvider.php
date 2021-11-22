<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Plugin\UiComponent;

use Magento\TwoFactorAuth\Api\Data\UserConfigInterface;
use Magento\TwoFactorAuth\Ui\Component\Form\User\DataProvider;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

class UserDataProvider
{
    /** @var UserConfigManagerInterface */
    private $userConfigManager;

    public function __construct(
        UserConfigManagerInterface $userConfigManager
    ) {
        $this->userConfigManager = $userConfigManager;
    }

    public function afterGetData(DataProvider $subject, $result)
    {
        $userId = $subject->getCollection()->getFirstItem()->getId();
        if ($userId) {
            $result[$userId][UserConfigInterface::DEFAULT_PROVIDER] = $this->userConfigManager->getDefaultProvider($userId);
        }
        return $result;
    }
}
