<?php declare(strict_types=1);

namespace LoyaltyProgram\Service\Discount;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class DiscountController extends StorefrontController
{
    #[Route(path: '/loyalty-claim/discount', name: 'frontend.discount.loyalty-item.add', defaults: ['_loginRequired' => true, 'XmlHttpRequest' => true],  methods: ['POST'])]
    public function addDiscountCode(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {

    }
}