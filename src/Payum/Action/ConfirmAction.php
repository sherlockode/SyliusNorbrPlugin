<?php

namespace Sherlockode\SyliusNorbrPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Generic;
use Sherlockode\SyliusNorbrPlugin\Norbr\ApiCode;
use Sherlockode\SyliusNorbrPlugin\Norbr\ClientFactory;
use Sherlockode\SyliusNorbrPlugin\Payum\Request\Confirm;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * Class ConfirmAction
 */
class ConfirmAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * ConfirmAction constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Generic $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();
        $details = $payment->getDetails();

        if (!isset($details['norbr_order_id'])) {
            throw new LogicException('Invalid Norbr ID.');
        }

        $order = $this->clientFactory->getClient($this->api)->getOrder($details['norbr_order_id']);

        if (isset($order['transactions']) && is_iterable($order['transactions'])) {
            foreach ($order['transactions'] as $transaction) {
                if (ApiCode::CAPTURE_SUCCESSFUL === $transaction['status']) {
                    $details['norbr_transaction_id'] = $transaction['id'];
                    break;
                }
            }
        }

        $details['state'] = ApiCode::TRANSACTION_SUCCESSFUL;
        $payment->setDetails($details);
    }

    /**
     * @param Generic $request
     *
     * @return bool
     */
    public function supports($request): bool
    {
        return $request instanceof Confirm && $request->getModel() instanceof PaymentInterface;
    }
}
