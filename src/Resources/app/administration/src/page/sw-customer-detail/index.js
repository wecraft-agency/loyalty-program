
import template from './template.html.twig';
import './view/sw-customer-detail-loyalty-program';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Component.override('sw-customer-detail', {

    template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
});

Shopware.Module.register('loyalty-customer-tab', {
    routeMiddleware(next, currentRoute) {
        const customRouteName = 'sw.customer.detail.loyalty.program';

        if (
            currentRoute.name === 'sw.customer.detail'
            && currentRoute.children.every(
                (currentRoute) => currentRoute.name !== customRouteName
            )
        ) {
            currentRoute.children.push({
                name: customRouteName,
                path: '/sw/customer/detail/:id/loyaltyProgram',
                component: 'sw-customer-detail-loyalty-program',
                meta: {
                    parentPath: 'sw.customer.index',
                    privilege: 'customer.viewer'
                }
            });
        }

        next(currentRoute);
    }
})