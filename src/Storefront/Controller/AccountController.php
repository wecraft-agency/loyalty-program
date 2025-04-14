<?php declare(strict_types=1);

namespace LoyaltyProgram\Storefront\Controller;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractChangeCustomerProfileRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractChangeEmailRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractChangePasswordRoute;
use Shopware\Core\Checkout\Customer\SalesChannel\AbstractDeleteCustomerRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\AccountProfileController;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedHook;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoader;
use Shopware\Storefront\Page\Account\Profile\AccountProfilePageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\Context;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class AccountController extends StorefrontController {

    public function __construct(
        private readonly AccountOverviewPageLoader $overviewPageLoader,
        public EntityRepository $loyaltyRedemptionRepository,
        public EntityRepository $loyaltyRewardRepository
    ) {
    }

    #[Route(path: '/account/loyalty', name: 'frontend.account.loyalty.index', defaults: ['_loginRequired' => true, '_noStore' => true], methods: ['GET'])]
    public function loyaltyIndex(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $page = $this->overviewPageLoader->load($request, $context, $customer);
        $this->hook(new AccountOverviewPageLoadedHook($page, $context));

        // set customer static data
        $customerId = $customer->getId();

        // get redemptions
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customerId', [$customerId]));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        $criteria->addAssociation('order');

        // result
        $loyaltyRedemptionResult = $this->loyaltyRedemptionRepository->search($criteria, Context::createDefaultContext());

        // store items
        $redemptions = ($loyaltyRedemptionResult->getTotal() > 0) ? $loyaltyRedemptionResult->getElements() : null;

        return $this->renderStorefront('@LoyaltyProgram/storefront/page/account/loyalty/index.html.twig', [
            'page' => $page,
            'redemptions' => $redemptions
        ]);
    }

    #[Route(path: '/account/loyalty/history', name: 'frontend.account.loyalty.history', defaults: ['_loginRequired' => true, '_noStore' => true], methods: ['GET'])]
    public function loyaltyHistory(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $page = $this->overviewPageLoader->load($request, $context, $customer);
        $this->hook(new AccountOverviewPageLoadedHook($page, $context));

        // set customer static data
        $customerId = $customer->getId();

        // get redemptions
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customerId', [$customerId]));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        $criteria->addAssociation('order');

        // result
        $loyaltyRedemptionResult = $this->loyaltyRedemptionRepository->search($criteria, Context::createDefaultContext());

        // store items
        $redemptions = ($loyaltyRedemptionResult->getTotal() > 0) ? $loyaltyRedemptionResult->getElements() : null;

        return $this->renderStorefront('@LoyaltyProgram/storefront/page/account/loyalty/history/index.html.twig', [
            'page' => $page,
            'redemptions' => $redemptions
        ]);
    }

    #[Route(path: '/account/loyalty/rewards', name: 'frontend.account.loyalty.rewards', defaults: ['_loginRequired' => true, '_noStore' => true], methods: ['GET'])]
    public function loyaltyRewards(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $page = $this->overviewPageLoader->load($request, $context, $customer);
        $this->hook(new AccountOverviewPageLoadedHook($page, $context));

        // set customer static data
        $customerId = $customer->getId();

        // get redemptions
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addSorting(new FieldSorting('points', FieldSorting::ASCENDING));

        // result
        $loyaltyRedemptionResult = $this->loyaltyRewardRepository->search($criteria, Context::createDefaultContext());

        // store items
        $redemptions = ($loyaltyRedemptionResult->getTotal() > 0) ? $loyaltyRedemptionResult->getElements() : null;

        return $this->renderStorefront('@LoyaltyProgram/storefront/page/account/loyalty/rewards/index.html.twig', [
            'page' => $page,
            'rewards' => $redemptions
        ]);
    }

    #[Route(path: '/account/loyalty/ranks', name: 'frontend.account.loyalty.ranks', defaults: ['_loginRequired' => true, '_noStore' => true], methods: ['GET'])]
    public function loyaltyRanks(Request $request, SalesChannelContext $context, CustomerEntity $customer): Response
    {
        $page = $this->overviewPageLoader->load($request, $context, $customer);
        $this->hook(new AccountOverviewPageLoadedHook($page, $context));

        // set customer static data
        $customerId = $customer->getId();

        // get redemptions
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customerId', [$customerId]));
        $criteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));

        $criteria->addAssociation('order');

        // result
        $loyaltyRedemptionResult = $this->loyaltyRedemptionRepository->search($criteria, Context::createDefaultContext());

        // store items
        $redemptions = ($loyaltyRedemptionResult->getTotal() > 0) ? $loyaltyRedemptionResult->getElements() : null;

        return $this->renderStorefront('@LoyaltyProgram/storefront/page/account/loyalty/ranks/index.html.twig', [
            'page' => $page,
            'redemptions' => $redemptions
        ]);
    }
}