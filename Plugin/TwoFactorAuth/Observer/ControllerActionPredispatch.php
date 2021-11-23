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
     * @var HtmlAreaTokenVerifier
     */
    private $tokenManager;

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

    public function __construct(
        TfaInterface $tfa,
        TfaSessionInterface $tfaSession,
        HtmlAreaTokenVerifier $tokenManager,
        ActionFlag $actionFlag,
        UrlInterface $url,
        UserContextInterface $userContext
    ) {
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->tokenManager = $tokenManager;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
        $this->userContext = $userContext;
    }

    public function aroundExecute(BaseControllerActionPredispatch $subject, callable $proceed, Observer $observer)
    {
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $userId = $this->userContext->getUserId();
        $accessGranted = $this->tfaSession->isGranted();
        $this->tokenManager->readConfigToken();

        $fullActionName = $observer->getEvent()->getData('request')->getFullActionName();
        $allowedUrls = array_merge($this->tfa->getAllowedUrls(), ['tfa_rtfa_requestprovider']);

        if (in_array($fullActionName, $allowedUrls, true)) {
            //Actions that are used for 2FA must remain accessible.
            return;
        }

        if ($userId) {
            $defaultProvider = $this->tfa->getDefaultProviderCode($userId);
            if (!$accessGranted && (empty($defaultProvider) || is_null($defaultProvider))) {
                return $this->redirect('tfa/rtfa/requestprovider', $controllerAction);
            } else {
                return $proceed($observer);
            }
        }
    }

    /**
     * Redirect user to given URL.
     *
     * @param string $url
     * @return void
     */
    private function redirect(string $url, $controllerAction)
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $controllerAction->getResponse()->setRedirect($this->url->getUrl($url));
    }
}
