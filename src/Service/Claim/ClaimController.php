<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Claim;

use LoyaltyProgram\Service\LineItemHandler;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Checkout\Cart\CartException;
use Shopware\Core\Checkout\Cart\Error\Error;
use Shopware\Core\Checkout\Cart\LineItemFactoryRegistry;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Content\Product\Exception\ProductNotFoundException;
use Shopware\Core\Framework\Routing\RoutingException;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Profiling\Profiler;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Core\Checkout\Cart\Price\Struct\AbsolutePriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;

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

    #[Route(path: '/loyalty-claim/product', name: 'frontend.checkout.loyalty-item.add', defaults: ['_loginRequired' => true, 'XmlHttpRequest' => true],  methods: ['POST'])]
    public function add(Cart $cart, RequestDataBag $requestDataBag, Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        // collect points of actual databag
        $lineItems = $cart->getLineItems();
        $cartSpentPoints = 0;
        $cartAddSpentPoints = 0;


        // current cart points
        if ( count($lineItems->getElements()) > 0 ) {
            foreach ( $lineItems->getElements() as $lineItem ) {
                // Test if type is custom type, and has payload points
                if ( $lineItem->getType() === LineItemHandler::TYPE && isset($lineItem->getPayload()['points']) ) {
                    $quantity = $lineItem->getQuantity();
                    $points = $lineItem->getPayload()['points'];

                    $cartSpentPoints = $cartSpentPoints + ($quantity * $points);
                }
            }
        }

        // new lineitems
        $newLineItems = $requestDataBag->get('lineItems');

        foreach ( $newLineItems as $lineItemData ) {
            $item = $this->factory->create($this->getLineItemArray($lineItemData, [
                'quantity' => 1,
                'stackable' => true,
                'removable' => true,
            ]), $context);

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $item->getId()));

            // get redemption
            $loyaltyRedemptionResult = $this->loyaltyRewardRepository->search($criteria, Context::createDefaultContext());

            // get result
            $loyaltyRedemptionItem = $loyaltyRedemptionResult->getTotal() > 0 ? $loyaltyRedemptionResult->getElements()[$item->getId()] : null;

            if ( !is_null($loyaltyRedemptionItem) ) {
                $quantity = $item->getQuantity();
                $points = $loyaltyRedemptionItem->getPoints();

                $cartAddSpentPoints = $cartAddSpentPoints + ($quantity * $points);
            }
        }

        $cartSpentPoints = $cartSpentPoints + $cartAddSpentPoints;

        return Profiler::trace('cart::add-line-item', function () use ($cart, $requestDataBag, $request, $context, $customer, $cartSpentPoints) {
            /** @var RequestDataBag|null $lineItems */
            $lineItems = $requestDataBag->get('lineItems');
            if (!$lineItems) {
                throw RoutingException::missingRequestParameter('lineItems');
            }

            $count = 0;

            // Validate added rewards, by checking if points of customer are enough
            $customerPoints = isset($customer->getExtensions()['loyaltyCustomer']) ? $customer->getExtensions()['loyaltyCustomer']->getPoints() : null;

            try {
                if ( is_null($customerPoints) || ($cartSpentPoints > $customerPoints) ) {
                    $this->addFlash(self::DANGER, $this->trans('loyaltyProgram.global.addToCartPointsError'));
                } else {
                    $items = [];
                    /** @var RequestDataBag $lineItemData */
                    foreach ($lineItems as $lineItemData) {
                        try {
                            $item = $this->factory->create($this->getLineItemArray($lineItemData, [
                                'quantity' => 1,
                                'stackable' => true,
                                'removable' => true,
                            ]), $context);

                            // Explicitly set stackable to true
                            $item->setStackable(true);

                            // Explicitly set removable to true
                            $item->setRemovable(true);

                            // Get redemption by id
                            $criteria = new Criteria();
                            $criteria->addFilter(new EqualsFilter('id', $item->getId()));

                            // get redemption
                            $loyaltyRedemptionResult = $this->loyaltyRewardRepository->search($criteria, Context::createDefaultContext());

                            // get result
                            $loyaltyRedemptionItem = $loyaltyRedemptionResult->getTotal() > 0 ? $loyaltyRedemptionResult->getElements()[$item->getId()] : null;

                            // Set points for the line item
                            $item->setPayloadValue('points', $loyaltyRedemptionItem->getPoints());

                            // Set a label for the line item
                            $item->setLabel($lineItemData->get('label', $loyaltyRedemptionItem->getName()));

                            // Set a zero price for the line item
                            $priceDefinition = new AbsolutePriceDefinition(0, null);
                            $calculatedTaxes = new CalculatedTaxCollection([]);
                            $taxRules = new TaxRuleCollection([]);
                            $calculatedPrice = new CalculatedPrice(0, 0, $calculatedTaxes, $taxRules);
                            $item->setPrice($calculatedPrice);

                            $count += $item->getQuantity();

                            $items[] = $item;
                        } catch (CartException $e) {
                            if ($e->getErrorCode() === CartException::CART_INVALID_LINE_ITEM_QUANTITY_CODE) {
                                $this->addFlash(
                                    self::DANGER,
                                    $this->trans(
                                        'error.CHECKOUT__CART_INVALID_LINE_ITEM_QUANTITY',
                                        [
                                            '%quantity%' => $e->getParameter('quantity'),
                                        ]
                                    )
                                );

                                return $this->createActionResponse($request);
                            }

                            throw $e;
                        }
                    }

                    $cart = $this->cartService->add($cart, $items, $context);

                    if (!$this->traceErrors($cart)) {
                        $this->addFlash(self::SUCCESS, $this->trans('loyaltyProgram.global.addToCartSuccess', ['%count%' => $count]));
                    }
                }

            } catch (ProductNotFoundException|RoutingException) {
                $this->addFlash(self::DANGER, $this->trans('loyaltyProgram.global.addToCartError'));
            }

            return $this->createActionResponse($request);
        });
    }

    private function traceErrors(Cart $cart): bool
    {
        if ($cart->getErrors()->count() <= 0) {
            return false;
        }

        $this->addCartErrors($cart, fn (Error $error) => $error->isPersistent());

        return true;
    }

    private function getLineItemArray(RequestDataBag $lineItemData, ?array $defaultValues): array
    {
        if ($lineItemData->has('payload')) {
            $payload = $lineItemData->get('payload');

            if (mb_strlen($payload, '8bit') > (1024 * 256)) {
                throw RoutingException::invalidRequestParameter('payload');
            }

            $lineItemData->set('payload', json_decode($payload, true, 512, \JSON_THROW_ON_ERROR));
        }

        $lineItemArray = $lineItemData->all();
        if ($defaultValues !== null) {
            $lineItemArray = array_replace($defaultValues, $lineItemArray);
        }

        if (isset($lineItemArray['quantity'])) {
            $lineItemArray['quantity'] = (int) $lineItemArray['quantity'];
        }

        if (isset($lineItemArray['stackable'])) {
            $lineItemArray['stackable'] = (bool) $lineItemArray['stackable'];
        }

        if (isset($lineItemArray['removable'])) {
            $lineItemArray['removable'] = (bool) $lineItemArray['removable'];
        }

        if (isset($lineItemArray['priceDefinition']['quantity'])) {
            $lineItemArray['priceDefinition']['quantity'] = (int) $lineItemArray['priceDefinition']['quantity'];
        }

        if (isset($lineItemArray['priceDefinition']['isCalculated'])) {
            $lineItemArray['priceDefinition']['isCalculated'] = (int) $lineItemArray['priceDefinition']['isCalculated'];
        }

        return $lineItemArray;
    }
}