<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusNorbrPlugin\Model\TokenInterface;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\PersistToken;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

/**
 * Class PersistTokenAction
 */
class PersistTokenAction implements ActionInterface
{
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
     * PersistTokenAction constructor.
     *
     * @param EntityManagerInterface   $em
     * @param CustomerContextInterface $customerContext
     * @param string                   $tokenModel
     */
    public function __construct(EntityManagerInterface $em, CustomerContextInterface $customerContext, string $tokenModel)
    {
        $this->em = $em;
        $this->customerContext = $customerContext;
        $this->tokenModel = $tokenModel;
    }

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        if (empty($this->tokenModel)) {
            return;
        }
        
        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (empty($details['card']['token'])) {
            throw new LogicException('The token is empty.');
        }

        if (empty($details['card']['scheme'])) {
            throw new LogicException('The card scheme is empty.');
        }

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        if ($customer) {
            /** @var TokenInterface $token */
            $token = new $this->tokenModel;
            $token->setCustomer($customer);
            $token->setToken($details['card']['token']);
            $token->setScheme($details['card']['scheme']);
            $this->em->persist($token);
        }
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof PersistToken && $request->getModel() instanceof PaymentInterface;
    }
}
