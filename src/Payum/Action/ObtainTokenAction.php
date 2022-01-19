<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\ObtainToken;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ObtainTokenAction
 */
class ObtainTokenAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * ObtainTokenAction constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        /** @var $request ObtainToken */
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (!empty($details['card']['token'])) {
            throw new LogicException('The token has already been set.');
        }

        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        if (
            $getHttpRequest->method == 'POST' &&
            isset($getHttpRequest->request['norbr-token']) &&
            isset($getHttpRequest->request['norbr-customer_scheme_name'])
        ) {
            $details['card'] = [
                'scheme' => $getHttpRequest->request['norbr-customer_scheme_name'],
                'token' => $getHttpRequest->request['norbr-token'],
            ];
            $payment->setDetails($details);

            return;
        }

        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if ($user && $user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();
        }

        $renderTemplate = new RenderTemplate('@SherlockodeSyliusNorbrPlugin/Action/obtain_token.html.twig', [
            'publishable_key' => $this->api->getApiKey(),
            'is_production' => $this->api->isProduction(),
            'actionUrl' => $request->getToken() ? $request->getToken()->getTargetUrl() : null,
            'order' => $payment->getOrder(),
            'can_persist_card' => isset($customer),
        ]);
        $this->gateway->execute($renderTemplate);

        throw new HttpResponse($renderTemplate->getResult());
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof ObtainToken && $request->getModel() instanceof PaymentInterface;
    }
}
