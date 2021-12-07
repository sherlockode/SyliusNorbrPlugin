<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Sherlockode\SyliusNorbrPlugin\Norbr\ApiCode;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

/**
 * Class StatusAction
 */
class StatusAction implements ActionInterface
{
    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var SyliusPaymentInterface $payment */
        $payment = $request->getFirstModel();
        $details = $payment->getDetails();

        if (!isset($details['result']) || !isset($details['result']['code'])) {
            $request->markFailed();

            return;
        }

        if (!in_array($details['result']['code'], ApiCode::getSuccessCodes())) {
            $request->markFailed();

            return;
        }

        $request->markCaptured();
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof SyliusPaymentInterface;
    }
}
