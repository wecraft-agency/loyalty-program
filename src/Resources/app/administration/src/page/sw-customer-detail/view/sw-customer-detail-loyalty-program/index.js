import template from './template.html.twig';

const { Criteria } = Shopware.Data;

Shopware.Component.register('sw-customer-detail-loyalty-program', {
    template,

    compatConfig: Shopware.compatConfig,

    inject: ['repositoryFactory'],

    props: {
        customer: {
            type: Object,
            required: true
        }
    },

    metaInfo() {
        return {
            title: this.$tc('loyalty-customer.tabTitle')
        };
    },
});