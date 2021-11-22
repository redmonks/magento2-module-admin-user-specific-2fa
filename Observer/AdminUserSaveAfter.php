<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Observer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

class AdminUserSaveAfter implements ObserverInterface
{
    /** @var UserConfigManagerInterface */
    private $userConfigManager;

    /** @var AuthorizationInterface */
    private $authorization;

    public function __construct(
        UserConfigManagerInterface $userConfigManager,
        AuthorizationInterface $authorization
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->authorization = $authorization;
    }

    public function execute(Observer $observer)
    {
        if ($this->authorization->isAllowed('Magento_TwoFactorAuth::tfa')) {
            $user = $observer->getEvent()->getObject();
            $data = $user->getData();
            if (isset($data['default_provider'])) {
                $this->userConfigManager->setDefaultProvider((int)$user->getId(), $data['default_provider']);
                unset($data['default_provider']);
            }
        }
    }
}
