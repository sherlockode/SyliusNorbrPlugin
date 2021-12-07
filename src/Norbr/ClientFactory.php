<?php

namespace Sherlockode\SyliusNorbrPlugin\Norbr;

use Sherlockode\SyliusNorbrPlugin\Payum\NorbrApi;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ClientFactory
 */
class ClientFactory
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ClientFactory constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack          $requestStack
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param NorbrApi $api
     *
     * @return Client
     */
    public function getClient(NorbrApi $api): Client
    {
        return new Client($this->urlGenerator, $this->requestStack, $api);
    }
}
