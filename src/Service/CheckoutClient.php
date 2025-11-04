<?php

namespace App\Service;

use App\Entity\Product;
use Checkout\CheckoutApi;
use Checkout\CheckoutSdk;
use Checkout\Common\Address;
use Checkout\Common\Currency;
use Checkout\Common\CustomerRequest;
use Checkout\Environment;
use Checkout\Payments\Product as PaymentsProduct;
use Checkout\Payments\ThreeDsRequest;
use Checkout\Payments\Sessions\Billing;
use Checkout\Payments\Sessions\Card;
use Checkout\Payments\Sessions\PaymentMethodConfiguration;
use Checkout\Payments\Sessions\PaymentSessionsRequest;
use Symfony\Component\Routing\RouterInterface;

class CheckoutClient
{
    private RouterInterface $router;
    private string $processingChannelId;
    private CheckoutApi $checkoutAPI;

    public function __construct(RouterInterface $router, string $checkoutPrivateKey, string $processingChannelId)
    {
        $this->router = $router;
        $this->processingChannelId = $processingChannelId;

        $this->checkoutAPI = CheckoutSdk::builder()->staticKeys()
            ->secretKey($checkoutPrivateKey)
            ->environment(Environment::sandbox())
            ->build();
    }

    public function createPaymentSession(Product $product): array
    {
        $address = new Address();
        $address->address_line1 = '5 rue de la Terasse';
        $address->city = 'Paris';
        $address->zip = '75017';
        $address->country = 'FR';

        $billing = new Billing();
        $billing->address = $address;

        $customer = new CustomerRequest();
        $customer->name = 'Jean Edouard';
        $customer->email = 'customer2@email.fr';
        //$customer->id = 'cus_5xu7iipdwd5utf6oirclbl754q';

        $item = new PaymentsProduct();
        $item->name = $product->getName();
        $item->quantity = 1;
        $item->unit_price = $product->getPrice();
        $item->total_amount = $product->getPrice();

        $card = new Card();
        $card->store_payment_details = 'collect_consent';

        $storedCard = new \stdClass();
        $storedCard->customer_id = 'cus_5xu7iipdwd5utf6oirclbl754q';

        $paymentMethodConfiguration = new PaymentMethodConfiguration();
        $paymentMethodConfiguration->card = $card;
        $paymentMethodConfiguration->stored_card = $storedCard;

        $customer_retry = new \stdClass();
        $customer_retry->max_attempts = 1;

        $threeDS = new ThreeDsRequest();
        $threeDS->enabled = true;

        $paymentSessionRequest = new PaymentSessionsRequest();
        $paymentSessionRequest->amount = $product->getPrice();
        $paymentSessionRequest->currency = Currency::$EUR;
        $paymentSessionRequest->customer = $customer;
        $paymentSessionRequest->items[] = $item;
        $paymentSessionRequest->three_ds = $threeDS;
        $paymentSessionRequest->success_url = $this->router->generate('app_flow_success', [], RouterInterface::ABSOLUTE_URL);
        $paymentSessionRequest->failure_url = $this->router->generate('app_flow_fail', [], RouterInterface::ABSOLUTE_URL);
        $paymentSessionRequest->reference = strtoupper(substr(bin2hex(random_bytes(8)), 0, 12));
        $paymentSessionRequest->billing = $billing;
        //$paymentSessionRequest->customer_retry = $customer_retry;
        //$paymentSessionRequest->payment_method_configuration = $paymentMethodConfiguration;
        $paymentSessionRequest->processing_channel_id = $this->processingChannelId;

        return $this->checkoutAPI->getPaymentSessionsClient()->createPaymentSessions($paymentSessionRequest);
    }
}
