<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum;

/**
 * Class NorbrApi
 */
class NorbrApi
{
    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var bool
     */
    private $production;

    /**
     * NorbrApi constructor.
     *
     * @param string $merchantId
     * @param string $apiKey
     * @param bool   $production
     */
    public function __construct(string $merchantId, string $apiKey, bool $production)
    {
        $this->merchantId = $merchantId;
        $this->apiKey = $apiKey;
        $this->production = $production;
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->production;
    }
}
