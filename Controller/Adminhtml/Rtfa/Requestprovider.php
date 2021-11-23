<?php
namespace RedMonks\UserSpecificTwoFactorAuth\Controller\Adminhtml\Rtfa;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

class Requestprovider extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /** @var UserContextInterface */
    private $userContext;

    /** @var UserConfigManagerInterface */
    private $configManager;

    public function __construct(
        Context $context,
        UserContextInterface $userContext,
        UserConfigManagerInterface $configManager
    ) {
        parent::__construct($context);
        $this->userContext = $userContext;
        $this->configManager = $configManager;
    }

    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_TwoFactorAuth::tfa';

    /**
     * @inheritDoc
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->_isAllowed()) {
            $this->_response->setStatusHeader(403, '1.1', 'Forbidden');
            return $this->_redirect('*/auth/login');
        }
        return parent::dispatch($request);
    }

    public function execute()
    {
        if ($this->getRequest()->isPost() && $this->_formKeyValidator->validate($this->getRequest())) {
            $userId = $this->userContext->getUserId();
            $data = $this->getRequest()->getParams();
            $this->configManager->setDefaultProvider($userId, $data['default_provider']);
            $this->_redirect('tfa/tfa/requestconfig');
        } else {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        }
    }
}
