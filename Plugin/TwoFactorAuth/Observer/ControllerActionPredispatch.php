<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Plugin\TwoFactorAuth\Observer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\UrlInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;
use Magento\TwoFactorAuth\Observer\ControllerActionPredispatch as BaseControllerActionPredispatch;

class ControllerActionPredispatch
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenManager;

    public function __construct(
        TfaInterface $tfa,
        TfaSessionInterface $tfaSession,
        ActionFlag $actionFlag,
        UrlInterface $url,
        UserContextInterface $userContext,
        HtmlAreaTokenVerifier $tokenManager
    ) {
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
        $this->userContext = $userContext;
        $this->tokenManager = $tokenManager;
    }

    public function aroundExecute(BaseControllerActionPredispatch $subject, callable $proceed, Observer $observer)
    {
        $this->tokenManager->readConfigToken();
        if (in_array(
                $observer->getEvent()->getData('request')->getFullActionName(),
                array_merge($this->tfa->getAllowedUrls(), ['tfa_rtfa_requestprovider']), true
        )) {
            //Actions that are used for 2FA must remain accessible.
            return;
        }

        if ($userId = $this->userContext->getUserId()) {
            $defaultProvider = $this->tfa->getDefaultProviderCode($userId);
            if (!$this->tfaSession->isGranted() && (empty($defaultProvider) || is_null($defaultProvider))) {
                $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                $observer->getEvent()->getData('controller_action')
                    ->getResponse()
                    ->setRedirect($this->url->getUrl('tfa/rtfa/requestprovider'));
            } else {
                return $proceed($observer);
            }
        }
    }
}
