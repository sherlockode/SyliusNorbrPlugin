<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Sherlockode\SyliusNorbrPlugin\Payum\Action\StatusAction;

/**
 * Class NorbrPaymentGatewayFactory
 */
class NorbrPaymentGatewayFactory extends GatewayFactory
{
    /**
     * @param ArrayObject $config
     */
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'norbr',
            'payum.factory_title' => 'Norbr',
            'payum.action.status' => new StatusAction(),
        ]);

        $config['payum.api'] = function (ArrayObject $config) {
            return new NorbrApi(
                $config['merchant_id'],
                $config['api_key'],
                (bool)$config['production']
            );
        };

        $config['payum.paths'] = array_replace([
            'SherlockodeSyliusNorbrPlugin' => __DIR__.'/../Resources/views',
        ], $config['payum.paths'] ?: []);
    }
}
