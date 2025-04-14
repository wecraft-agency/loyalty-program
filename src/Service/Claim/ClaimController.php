<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Claim;

use LoyaltyProgram\Service\LineItemHandler;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Event\StorefrontRedirectEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Core\Checkout\Cart\Cart;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ClaimController extends StorefrontController
{
    private LineItemFactoryRegistry $factory;

    private CartService $cartService;

    public function __construct(LineItemFactoryRegistry $factory, CartService $cartService)
    {
        $this->factory = $factory;
        $this->cartService = $cartService;
    }

    #[Route(path: '/loyalty-claim/product', name: 'frontend.checkout.loyalty-item.add', methods: ['POST'])]
    public function add(Cart $cart, SalesChannelContext $context): Response
    {
        // Create product line item
        $lineItem = $this->factory->create([
            'type' => LineItemHandler::TYPE, // Results in 'reward-product'
            'referencedId' => Uuid::randomHex(), // this is not a valid UUID, change this to your actual ID!
            'quantity' => 1,
            'payload' => ['key' => 'value']
        ], $context);

        $this->cartService->add($cart, $lineItem, $context);


        // redirect to rewards
        return $this->redirectToRoute('frontend.account.loyalty.rewards');
    }
}