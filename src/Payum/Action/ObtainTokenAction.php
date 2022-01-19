<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Doctrine\ORM\EntityManagerInterface;
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
use Sherlockode\SyliusNorbrPlugin\Model\TokenInterface;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\ObtainToken;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

/**
 * Class ObtainTokenAction
 */
class ObtainTokenAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @var string
     */
    private $tokenModel;

    /**
     * ObtainTokenAction constructor.
     *
     * @param EntityManagerInterface   $em
     * @param CustomerContextInterface $customerContext
     * @param string                   $tokenModel
     */
    public function __construct(
        EntityManagerInterface $em,
        CustomerContextInterface $customerContext,
        string $tokenModel
    ) {
        $this->em = $em;
        $this->customerContext = $customerContext;
        $this->tokenModel = $tokenModel;
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

        $customer = $this->customerContext->getCustomer();
        $tokenRepository = $this->em->getRepository($this->tokenModel);

        $getHttpRequest = new GetHttpRequest();
        $this->gateway->execute($getHttpRequest);

        if ($getHttpRequest->method == 'POST') {
            $requestData = $getHttpRequest->request;

            if (
                isset($requestData['norbr-token']) &&
                isset($requestData['norbr-customer_scheme_name']) &&
                isset($requestData['norbr-card_number'])
            ) {
                $cardNumber = trim($requestData['norbr-card_number']);
                $cardNumber = substr($cardNumber, -4);

                $details['card'] = [
                    'scheme' => $requestData['norbr-customer_scheme_name'],
                    'token' => $requestData['norbr-token'],
                    'last4' => $cardNumber,
                ];
                $payment->setDetails($details);

                return;
            }

            if ($customer && isset($requestData['norbr-card'])) {
                /** @var TokenInterface $token */
                $token = $tokenRepository->findOneBy([
                    'id' => (int)$requestData['norbr-card'],
                    'customer' => $customer,
                ]);

                if (!$token) {
                    throw new LogicException('Invalid card selected.');
                }

                $details['card'] = [
                    'scheme' => $token->getScheme(),
                    'token' => $token->getToken(),
                    'last4' => $token->getLast4(),
                ];
                $payment->setDetails($details);

                return;
            }
        }

        $renderTemplate = new RenderTemplate('@SherlockodeSyliusNorbrPlugin/Action/obtain_token.html.twig', [
            'publishable_key' => $this->api->getApiKey(),
            'is_production' => $this->api->isProduction(),
            'actionUrl' => $request->getToken() ? $request->getToken()->getTargetUrl() : null,
            'order' => $payment->getOrder(),
            'can_persist_card' => isset($customer),
            'available_cards' => $tokenRepository->findBy(['customer' => $customer]),
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
