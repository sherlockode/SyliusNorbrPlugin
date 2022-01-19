<?php

namespace Sherlockode\SyliusNorbrPlugin\Model;

use Sylius\Component\Core\Model\CustomerInterface;

interface TokenInterface
{
    /**
     * @return CustomerInterface|null
     */
    public function getCustomer(): ?CustomerInterface;

    /**
     * @param CustomerInterface|null $customer
     *
     * @return $this
     */
    public function setCustomer(?CustomerInterface $customer): self;

    /**
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * @param string|null $token
     *
     * @return $this
     */
    public function setToken(?string $token): self;
}
