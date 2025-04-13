import template from './template.html.twig';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Component.override('sw-customer-card', {
    template,

    inject: ['repositoryFactory'],

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    computed: {
        loyaltyPoints: {
            get() {
                if (!this.customer.extensions.loyaltyCustomer) {
                    const loyaltyCustomer = this.repositoryFactory.create('loyalty_customer').create(Shopware.Context.api);
                    loyaltyCustomer.customerId = this.customer.id; // Set the customerId
                    this.$set(this.customer.extensions, 'loyaltyCustomer', loyaltyCustomer);

                    // Save the entity to the database
                    const loyaltyCustomerRepository = this.repositoryFactory.create('loyalty_customer');
                    loyaltyCustomerRepository.save(this.customer.extensions.loyaltyCustomer, Shopware.Context.api);
                }

                return this.customer.extensions.loyaltyCustomer?.points ?? null;
            }
        },
        loyaltyPointsPending: {
            get() {
                if (!this.customer.extensions.loyaltyCustomer) {
                    const loyaltyCustomer = this.repositoryFactory.create('loyalty_customer').create(Shopware.Context.api);
                    loyaltyCustomer.customerId = this.customer.id; // Set the customerId
                    this.$set(this.customer.extensions, 'loyaltyCustomer', loyaltyCustomer);

                    // Save the entity to the database
                    const loyaltyCustomerRepository = this.repositoryFactory.create('loyalty_customer');
                    loyaltyCustomerRepository.save(this.customer.extensions.loyaltyCustomer, Shopware.Context.api);
                }

                return this.customer.extensions.loyaltyCustomer?.pointsPending ?? null;
            }
        },
        loyaltyPointsTotal: {
            get() {
                if (!this.customer.extensions.loyaltyCustomer) {
                    const loyaltyCustomer = this.repositoryFactory.create('loyalty_customer').create(Shopware.Context.api);
                    loyaltyCustomer.customerId = this.customer.id; // Set the customerId
                    this.$set(this.customer.extensions, 'loyaltyCustomer', loyaltyCustomer);

                    // Save the entity to the database
                    const loyaltyCustomerRepository = this.repositoryFactory.create('loyalty_customer');
                    loyaltyCustomerRepository.save(this.customer.extensions.loyaltyCustomer, Shopware.Context.api);
                }

                return this.customer.extensions.loyaltyCustomer?.pointsTotal ?? null;
            }
        },
    },
})