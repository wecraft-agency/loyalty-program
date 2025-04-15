<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Discount;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class DiscountController extends StorefrontController
{

    private EntityRepository $loyaltyRewardRepository;

    public function __construct(EntityRepository $loyaltyRewardRepository)
    {
        $this->loyaltyRewardRepository = $loyaltyRewardRepository;
    }

    #[Route(path: '/loyalty-claim/discount', name: 'frontend.discount.loyalty-reward.add', defaults: ['_loginRequired' => true, 'XmlHttpRequest' => true],  methods: ['POST', 'GET'])]
    public function add(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {

        ;
    }
}