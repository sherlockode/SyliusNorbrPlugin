<?php

namespace Sherlockode\SyliusNorbrPlugin\Processor;

use Sherlockode\SyliusNorbrPlugin\Norbr\ClientFactory;
use Sherlockode\SyliusNorbrPlugin\Payum\NorbrApi;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

/**
 * Class RefundProcessor
 */
class RefundProcessor
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * RefundProcessor constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @throws UpdateHandlingException
     */
    public function refund(PaymentInterface $payment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();

        if ($gatewayConfig->getGatewayName() !== 'norbr') {
            return;
        }

        $config = $gatewayConfig->getConfig();
        $api = new NorbrApi($config['merchant_id'], $config['api_key'], (bool)$config['production']);

        $details = $payment->getDetails();

        if (!isset($details['norbr_order_id'])) {
            throw new UpdateHandlingException('Could not refund Norbr order');
        }

        $this->clientFactory->getClient($api)->refund($payment);
    }
}
