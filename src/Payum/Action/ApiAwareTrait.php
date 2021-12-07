<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Payum\Core\Exception\UnsupportedApiException;
use Sherlockode\SyliusNorbrPlugin\Payum\NorbrApi;

/**
 * Trait ApiAwareTrait
 */
trait ApiAwareTrait
{
    /**
     * @var NorbrApi
     */
    private $api;

    /**
     * @param NorbrApi $api
     */
    public function setApi($api): void
    {
        if (!$api instanceof NorbrApi) {
            throw new UnsupportedApiException('Not supported. Expected an instance of ' . NorbrApi::class);
        }

        $this->api = $api;
    }
}
