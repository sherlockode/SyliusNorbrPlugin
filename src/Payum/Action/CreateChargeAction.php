<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use GuzzleHttp\Exception\GuzzleException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusNorbrPlugin\Norbr\ClientFactory;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\CreateCharge;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class CreateChargeAction
 */
class CreateChargeAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

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

        $charge = $this->clientFactory->getClient($this->api)->createCharge($payment);
        $details['result'] = $charge['result'];
        $payment->setDetails($details);
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
