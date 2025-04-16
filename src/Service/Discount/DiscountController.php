<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Discount;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Checkout\Promotion\Util\PromotionCodeService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class DiscountController extends StorefrontController
{

    private EntityRepository $loyaltyRewardRepository;

    private EntityRepository $promotionRepository;

    public function __construct(EntityRepository $loyaltyRewardRepository, EntityRepository $discountRepository)
    {
        $this->loyaltyRewardRepository = $loyaltyRewardRepository;
        $this->promotionRepository = $discountRepository;
    }

    #[Route(path: '/loyalty-claim/discount', name: 'frontend.discount.loyalty-reward.add', defaults: ['_loginRequired' => true, 'XmlHttpRequest' => true],  methods: ['POST', 'GET'])]
    public function add(Request $request, SalesChannelContext $context, CustomerEntity $customer, PromotionCodeService $promotionCodeService): Response
    {

        // @todo: logic for redirect to reward page
        // @todo: change points of customer, when redeem discount/create discount successfull

        // get data, set data
        $discountType = isset($_POST['discountMethod']) && $_POST['discountMethod'] === 'fixed' ? 'absolute' : 'percentage';
        $discountValue = isset($_POST['discountValue']) ? $_POST['discountValue'] : 0;
        $discountName = isset($_POST['discountName']) ? $_POST['discountName'] : 'Discount';

        $this->promotionRepository->create(
            [
                [
                    'id' => Uuid::randomHex(),
                    'name' => $discountName,
                    'active' => true,
                    'useCodes' => true,
                    'maxRedemptionsPerCustomer' => 1,
                    'maxRedemptionsGlobal' => 1,
                    'useSetGroups' => false,
                    'salesChannels' => [
                        ['salesChannelId' => $context->getSalesChannelId(), 'priority' => 1],
                    ],
                    'code' => $promotionCodeService->getFixedCode(),
                    'discounts' => [
                        [
                            'scope' => 'cart',
                            'type' => $discountType,
                            'value' => $discountValue
                        ]
                    ],
                    'personaCustomers' => [
                        [
                            'id' => $customer->getId(),
                            'salesChannelId' => $context->getSalesChannelId()
                        ]
                    ]
                ]
            ],
            $context->getContext()
        );
    }
}