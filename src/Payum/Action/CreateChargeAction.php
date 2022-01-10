<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use GuzzleHttp\Exception\GuzzleException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Generic;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;
use Sherlockode\SyliusNorbrPlugin\Norbr\ApiCode;
use Sherlockode\SyliusNorbrPlugin\Norbr\ClientFactory;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\CreateCharge;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class CreateChargeAction
 */
class CreateChargeAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * CreateChargeAction constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Generic $request
     *
     * @throws LogicException
     * @throws GuzzleException
     */
    public function execute($request): void
    {
        /** @var $request CreateCharge */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (empty($details['card']['token'])) {
            throw new LogicException('The card token has to be set.');
        }

        if (empty($details['card']['scheme'])) {
            throw new LogicException('The card scheme has to be set.');
        }

        $successToken = $this->tokenFactory->createToken('norbr', $payment, 'norbr_capture_done');
        $failedToken = $this->tokenFactory->createToken('norbr', $payment, 'norbr_capture_failed');

        $charge = $this->clientFactory->getClient($this->api)->createCharge(
            $payment,
            $successToken->getTargetUrl(),
            $failedToken->getTargetUrl()
        );
        $details['norbr_order_id'] = $charge['order_id'] ?? null;
        $payment->setDetails($details);

        if (isset($charge['redirect_url'])) {
            throw new HttpRedirect($charge['redirect_url']);
        } elseif (isset($charge['result']['code']) && in_array($charge['result']['code'], ApiCode::getSuccessCodes())) {
            throw new HttpRedirect($successToken->getTargetUrl());
        }

        throw new HttpRedirect($failedToken->getTargetUrl());
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof CreateCharge && $request->getModel() instanceof PaymentInterface;
    }
}
