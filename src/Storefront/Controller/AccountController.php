<?php declare(strict_types=1);

namespace LoyaltyProgram\Storefront\Controller;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoadedHook;
use Shopware\Storefront\Page\Account\Overview\AccountOverviewPageLoader;
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
        public EntityRepository $loyaltyRewardRepository,
        public EntityRepository $promotionRepository
    ) {
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
        $criteria->addFilter(new EqualsAnyFilter('status', ['finished', 'pending']));
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

        // get rewards
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addSorting(new FieldSorting('points', FieldSorting::ASCENDING));

        // result
        $loyaltyRewardsResult = $this->loyaltyRewardRepository->search($criteria, Context::createDefaultContext());

        // store items
        $rewards = ($loyaltyRewardsResult->getTotal() > 0) ? $loyaltyRewardsResult->getElements() : null;

        // search rewards by promotionCustomerPersona
        $promotionsCriteria = new Criteria();
        $promotionsCriteria->addFilter(new EqualsFilter('active', true));
        $promotionsCriteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));
        $promotionsCriteria->addAssociation('personaCustomers');

        // result
        $promotionsResults = $this->promotionRepository->search($promotionsCriteria, Context::createDefaultContext());

        // store items
        $promotions = ($promotionsResults->getTotal() > 0) ? $promotionsResults->getElements() : null;
        $promotionsFiltered = [];

        foreach ( $promotions as $promotion ) {
            if ( !empty($promotion->getPersonaCustomers()->first()) ) {
                if ( $promotion->getPersonaCustomers()->first()->getId() === $customerId ) {
                    $promotionsFiltered[] = $promotion;
                }
            }
        }

        return $this->renderStorefront('@LoyaltyProgram/storefront/page/account/loyalty/rewards/index.html.twig', [
            'page' => $page,
            'rewards' => $rewards,
            'promotions' => $promotionsFiltered
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