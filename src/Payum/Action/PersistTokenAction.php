<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusNorbrPlugin\Model\TokenInterface;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\PersistToken;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var string
     */
    private $tokenModel;

    /**
     * PersistTokenAction constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     * @param string                 $tokenModel
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, string $tokenModel)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
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

        $user = $this->tokenStorage->getToken() ? $this->tokenStorage->getToken()->getUser() : null;
        if ($user && $user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();
        }

        if (isset($customer)) {
            /** @var TokenInterface $token */
            $token = new $this->tokenModel;
            $token->setCustomer($customer);
            $token->setToken($details['card']['token']);
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
