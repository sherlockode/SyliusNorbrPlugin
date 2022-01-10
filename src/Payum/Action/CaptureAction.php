<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusNorbrPlugin\Norbr\ApiCode;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\CreateCharge;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\ObtainToken;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Request\Capture;

/**
 * Class CaptureAction
 */
class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (isset($details['state'])) {
            return;
        }

        if (empty($details['card']['token']) || empty($details['card']['scheme'])) {
            $obtainToken = new ObtainToken($request->getToken());
            $obtainToken->setModel($payment);

            $this->gateway->execute($obtainToken);
        }

        $this->gateway->execute(new CreateCharge($request->getToken()));
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof Capture && $request->getModel() instanceof PaymentInterface;
    }
}
