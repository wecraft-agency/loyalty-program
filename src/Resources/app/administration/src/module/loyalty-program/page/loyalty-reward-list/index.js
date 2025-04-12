import template from './loyalty-reward-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('loyalty-reward-list', {
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
            return this.repositoryFactory.create('loyalty_reward');
        },

        columns() {
            return this.getColumns();
        }
    },

    methods: {
        getList() {
            this.isLoading = true;
            const criteria = new Criteria(this.page, this.limit);

            this.entityRepository.search(criteria, Shopware.Context.api).then((items) => {
                this.total = items.total;
                this.items = items;
                this.isLoading = false;

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
                    property: 'name',
                    dataIndex: 'name',
                    inlineEdit: 'string',
                    routerLink: 'loyalty.program.rewards.detail',
                    label: this.$tc('loyalty-program.rewards.list.nameLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'type',
                    dataIndex: 'type',
                    inlineEdit: 'string',
                    label: this.$tc('loyalty-program.rewards.list.typeLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'points',
                    dataIndex: 'points',
                    inlineEdit: 'number',
                    label: this.$tc('loyalty-program.rewards.list.pointsLabel'),
                    allowResize: true,
                    primary: true
                },
                {
                    property: 'active',
                    label: this.$tc('loyalty-program.rewards.list.active'),
                    inlineEdit: 'boolean',
                },
            ]
        },

        onChangeLanguage(languageId) {
            this.getList(languageId)
        }
    }
})