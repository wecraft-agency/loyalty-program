import template from './loyalty-redemption-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('loyalty-redemption-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('listing'),
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            items: null,
            isLoading: false,
            showDeleteModal: false,
            repository: null,
            total: 0
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        entityRepository() {
            return this.repositoryFactory.create('loyalty_redemption');
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        },

        columns() {
            return this.getColumns();
        }
    },

    methods: {
        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);

            criteria.addAssociation('customer');
            criteria.addAssociation('order');
            criteria.addSorting(
                Criteria.sort('createdAt', 'DESC')
            )

            this.entityRepository.search(criteria, Shopware.Context.api).then((items) => {
                this.total = items.total;
                this.items = items;
                this.isLoading = false;

                console.log(items);

                return items;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        onDelete(id) {
            this.showDeleteModal = id
        },

        onCloseDeleteModal() {
            this.showDeleteModal = false;
        },

        getColumns() {
            return [
                {
                    property: 'createdAt',
                    dataIndex: 'createdAt',
                    label: this.$tc('loyalty-program.redemptions.list.dateLabel'),
                    allowResize: true,
                    primary: true

                },
                {
                    property: 'type',
                    dataIndex: 'type',
                    label: this.$tc('loyalty-program.redemptions.list.typeLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'status',
                    dataIndex: 'status',
                    label: this.$tc('loyalty-program.redemptions.list.statusLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'points',
                    dataIndex: 'points',
                    inlineEdit: 'number',
                    label: this.$tc('loyalty-program.redemptions.list.pointsLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'customerName',
                    dataIndex: 'customer.firstName,customer.lastName',
                    label: 'sw-order.list.columnCustomerName',
                    allowResize: true,
                },
                {
                    property: 'orderNumber',
                    dataIndex: 'order.orderNumber',
                    label: 'sw-order.list.columnOrderNumber',
                    allowResize: true,
                },
            ]
        },

        onChangeLanguage(languageId) {
            this.getList(languageId)
        }
    }
})