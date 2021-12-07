<?php

namespace Sherlockode\SyliusNorbrPlugin\Norbr;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Sherlockode\SyliusNorbrPlugin\Payum\NorbrApi;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Client
 */
class Client
{
    private const API_URL_SANDBOX = 'https://api-sandbox.norbr.io';
    private const API_URL_PRODUCTION = 'https://api.norbr.io';

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var NorbrApi
     */
    private $api;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * Client constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack          $requestStack
     * @param NorbrApi              $api
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, NorbrApi $api)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->api = $api;
    }

    /**
     * @param PaymentInterface $model
     *
     * @return array
     *
     * @throws GuzzleException
     */
    public function createCharge(PaymentInterface $model): array
    {
        $details = $model->getDetails();
        $order = $model->getOrder();
        $customer = $order->getCustomer();
        $address = $order->getBillingAddress();
        $amount = floor(abs($model->getAmount() / 100));

        try {
            $response = $this->getClient()->request(
                'POST',
                '/payment/order',
                [
                    'json' => [
                        'operation_type' => 'direct_capture',
                        'token' => $details['card']['token'],
                        'merchant_contract' => $this->api->getMerchantId(),
                        'payment_method_name' => $details['card']['scheme'],
                        'amount' => $amount,
                        'currency' => $model->getCurrencyCode(),
                        'order_merchant_id' => $order->getId(),
                        'payment_channel' => 'e-commerce',
                        'website_url' => $this->urlGenerator->generate(
                            'sylius_shop_homepage',
                            [],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                        'customer_id' => $customer->getId(),
                        'customer_email' => $customer->getEmail(),
                        'customer_first_name' => $customer->getFirstName(),
                        'customer_last_name' => $customer->getLastName(),
                        'customer_street_name' => $address->getStreet(),
                        'customer_city' => $address->getCity(),
                        'customer_zip_code' => $address->getPostcode(),
                        'customer_country' => $address->getCountryCode(),
                        'customer_ip' => $this->requestStack->getMainRequest()->getClientIp(),
                    ],
                    'headers' => [
                        'x-api-key' => $this->api->getApiKey(),
                        'version' => '1.0.0',
                    ],
                ],
            );
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return GuzzleClient
     */
    private function getClient(): GuzzleClient
    {
        if (!$this->client) {
            $this->client = new GuzzleClient([
                'base_uri' => $this->getBaseUri(),
                'timeout' => 30,
            ]);
        }

        return $this->client;
    }

    /**
     * @return string
     */
    private function getBaseUri(): string
    {
        if ($this->api->isProduction()) {
            return self::API_URL_PRODUCTION;
        }

        return self::API_URL_SANDBOX;
    }
}
