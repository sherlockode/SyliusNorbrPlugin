<?php

namespace Sherlockode\SyliusNorbrPlugin\Controller;

use Payum\Core\Payum;
use Sherlockode\SyliusNorbrPlugin\Norbr\ApiCode;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CheckoutController
 */
class CheckoutController
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * CheckoutController constructor.
     *
     * @param Payum $payum
     */
    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function captureDoneAction(Request $request)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);
        $identity = $token->getDetails();
        /** @var PaymentInterface $payment */
        $payment = $this->payum->getStorage($identity->getClass())->find($identity);
        $details = $payment->getDetails();

        $details['state'] = ApiCode::TRANSACTION_SUCCESSFUL;
        $payment->setDetails($details);

        $this->payum->getHttpRequestVerifier()->invalidate($token);
        $afterPayToken = $this->payum->getTokenFactory()->createToken(
            'norbr',
            $payment,
            'sylius_shop_order_after_pay'
        );

        return new RedirectResponse($afterPayToken->getTargetUrl());
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function captureFailedAction(Request $request)
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);
        $identity = $token->getDetails();
        /** @var PaymentInterface $payment */
        $payment = $this->payum->getStorage($identity->getClass())->find($identity);
        $details = $payment->getDetails();

        $details['state'] = ApiCode::TRANSACTION_FAILED;
        $payment->setDetails($details);

        $this->payum->getHttpRequestVerifier()->invalidate($token);
        $afterPayToken = $this->payum->getTokenFactory()->createToken(
            'norbr',
            $payment,
            'sylius_shop_order_after_pay'
        );

        return new RedirectResponse($afterPayToken->getTargetUrl());
    }
}
