<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CheckoutClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/', name: 'app_flow_')]
class FlowController extends AbstractController
{
    #[Route('', name: 'index', methods: 'GET')]
    public function elements(CheckoutClient $checkoutClient): Response
    {
        $testProduct = Product::generateTestProduct();

        return $this->render('flow/index.html.twig', [
            'product' => $testProduct
        ]);
    }

    #[Route('/payment-session', name: 'session', methods: 'POST')]
    public function paymentSession(CheckoutClient $checkoutClient, CacheInterface $filesystemAdapter): Response
    {
        
        try {
            $paymentSession = $checkoutClient->createPaymentSession(Product::generateTestProduct());
        } catch (\Checkout\CheckoutApiException $e) {
            $paymentSession = [];
        }

        return $this->json($paymentSession);
    }


    #[Route('/success', name: 'success', methods: 'GET')]
    public function success(): Response
    {
        return $this->render('flow/success.html.twig');
    }

    #[Route('/fail', name: 'fail', methods: 'GET')]
    public function fail(): Response
    {
        return $this->render('flow/fail.html.twig');
    }
}
