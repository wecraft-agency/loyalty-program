import './page/loyalty-reward-list';
import './page/loyalty-reward-detail';
import './page/loyalty-reward-create';

import enGB from  "./snippet/en-GB.json";
import deDE from "./snippet/de-DE.json";

Shopware.Module.register('loyalty-program', {
    type: 'core',
    name: 'loyalty',
    title: 'loyalty-program.general.mainMenuItemLabel',
    description: 'loyalty-program.general.mainMenuItemDescription',
    color: '#189eff',
    icon: 'regular-party-horn',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        'rewards.index': {
            component: 'loyalty-reward-list',
            path: 'reward/index',
        },
        'rewards.detail': {
            component: 'loyalty-reward-detail',
            path: 'reward/detail/:id',
            meta: {
                parentPath: 'loyalty.program.rewards.index'
            }
        },
        'rewards.create': {
            component: 'loyalty-reward-create',
            path: 'reward/create',
            meta: {
                parentPath: 'loyalty.program.rewards.index'
            }
        }
    },

    navigation: [
        {
            id: 'loyalty-program',
            label: 'loyalty-program.general.mainMenuItemLabel',
            color: '#189eff',
            icon: 'regular-party-horn',
            position: 50,
        },
        {
            id: 'loyalty-rewards',
            parent: 'loyalty-program',
            label: 'loyalty-program.general.rewardsLabel',
            path: 'loyalty.program.rewards.index',
            position: 10,
        },
        {
            id: 'loyalty-redemptions',
            parent: 'loyalty-program',
            label: 'loyalty-program.general.redemptionsLabel',
            path: 'sw.extension.store',
            position: 10,
        },
        {
            id: 'loyalty-campaigns',
            parent: 'loyalty-program',
            label: 'loyalty-program.general.campaignsLabel',
            path: 'sw.extension.store',
            position: 10,
        },
        {
            id: 'loyalty-ranks',
            parent: 'loyalty-program',
            label: 'loyalty-program.general.ranksLabel',
            path: 'sw.extension.store',
            position: 10,
        },
    ],
})