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

    /**
     * @return string|null
     */
    public function getScheme(): ?string;

    /**
     * @param string|null $scheme
     *
     * @return $this
     */
    public function setScheme(?string $scheme): self;

    /**
     * @return string|null
     */
    public function getLast4(): ?string;

    /**
     * @param string|null $last4
     *
     * @return $this
     */
    public function setLast4(?string $last4): self;
}
