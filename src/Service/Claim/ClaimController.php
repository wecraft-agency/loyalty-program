<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Claim;

use LoyaltyProgram\Service\LineItemHandler;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Event\StorefrontRedirectEvent;
use Shopware\Storefront\Framework\Routing\StorefrontResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Framework\Context;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ClaimController extends StorefrontController
{
    private LineItemFactoryRegistry $factory;

    private CartService $cartService;

    private EntityRepository $loyaltyRewardRepository;

    public function __construct(LineItemFactoryRegistry $factory, CartService $cartService, EntityRepository $loyaltyRewardRepository)
    {
        $this->factory = $factory;
        $this->cartService = $cartService;
        $this->loyaltyRewardRepository = $loyaltyRewardRepository;
    }

    #[Route(path: '/loyalty-claim/product', name: 'frontend.checkout.loyalty-item.add', methods: ['POST'])]
    public function add(Cart $cart, SalesChannelContext $context): Response
    {
        if ( $_POST['type'] === LineItemHandler::TYPE ) {
            // get reward by repository
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $_POST['id']));

            $loyaltyRewardResult = $this->loyaltyRewardRepository->search($criteria, Context::createDefaultContext());

            $lineItemPoints = 0;

            if ( $loyaltyRewardResult->getTotal() > 0 ) {
                $lineItemPoints = $loyaltyRewardResult->getElements()[$_POST['id']]->getPoints();
            }


            // Create product line item
            $lineItem = new LineItem($_POST['id'], LineItemHandler::TYPE, null, 1);
            $lineItem->setStackable(true);
            $lineItem->setRemovable(false);
            $lineItem->setLabel($_POST['name']);

            // Set a zero price for the line item
            $priceDefinition = new AbsolutePriceDefinition(0, null);
            $calculatedTaxes = new CalculatedTaxCollection([]);
            $taxRules = new TaxRuleCollection([]);
            $calculatedPrice = new CalculatedPrice(0, 0, $calculatedTaxes, $taxRules);
            $lineItem->setPrice($calculatedPrice);

            // Add custom payload for points
            $lineItem->setPayloadValue('points', $lineItemPoints); // Set the number of points

            // Add the line item to the cart
            $this->cartService->add($cart, $lineItem, $context);
        }


        return $this->renderStorefront('@Storefront/storefront/base.html.twig');
    }
}