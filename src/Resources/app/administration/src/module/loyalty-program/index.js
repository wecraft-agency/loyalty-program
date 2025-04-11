import "./snippet/en-GB.json";
import "./snippet/de-DE.json";

Shopware.Module.register('loyalty-program', {
    type: 'core',
    name: 'Example',
    title: 'loyalty-program.general.mainMenuItemGeneral',
    description: 'loyalty-program.general.descriptionTextModule',
    color: '#189eff',
    icon: 'regular-party-horn',
    routes: {
        index: {
            components: { },
            path: 'index',
        },
    },

    navigation: [
        {
            id: 'loyalty-program',
            label: 'loyalty-program.general.mainMenuItemGeneral',
            color: '#189eff',
            icon: 'regular-party-horn',
            position: 50,
        },
        {
            id: 'loyalty-rewards',
            label: 'Rewards',
            position: 10,
            parent: 'loyalty-program',
        },
        {
            id: 'loyalty-redemptions',
            label: 'Redemptions',
            position: 10,
            parent: 'loyalty-program',
        },
    ],
})