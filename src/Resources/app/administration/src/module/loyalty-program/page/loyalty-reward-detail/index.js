const { Component } = Shopware;
const { Criteria } = Shopware.Data;
const { Mixin } = Shopware;

import template from './loyalty-reward-detail.html.twig';

Component.register('loyalty-reward-detail', {
    template,

    inject: [
        'repositoryFactory',
        'context'
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    data() {
        return {
            item: null,
            isLoading: false,
            processSuccess: false,
            repository: null,
            isSaveSuccess: false,
            mediaItem: null,
            uploadTag: 'loyalty-program-reward-upload-tag',
            mediaDefaultFolderId: null,
            showMediaModal: false,
        }
    },

    watch: {
        'item.media.id'() {
            if (!this.item.media?.id) {
                return;
            }

            this.setMediaItem({ targetId: this.item.media.id });
        },
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    created() {
        this.createdComponent()
    },

    computed: {
        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        typeOptions() {
            return [{
                label: this.$tc('loyalty-program.rewards.detail.typeProductOption'),
                value: 'product',
            }, {
                label: this.$tc('loyalty-program.rewards.detail.typeDiscountOption'),
                value: 'discount',
            }];
        },

        discountMethodOptions() {
            return [
                {
                    label: this.$tc('loyalty-program.rewards.detail.discountMethodFixedOption'),
                    value: 'fixed',
                },
                {
                    label: this.$tc('loyalty-program.rewards.detail.discountMethodPercentageOption'),
                    value: 'percentage',
                }
            ]
        }
    },

    methods: {
        createdComponent() {
            this.repository = this.repositoryFactory.create('loyalty_reward');

            this.isLoading = true;

            this.loadAll().then(() => {
                this.isLoading = false;
            });
        },

        loadAll() {
            return Promise.all([
                this.getEntity()
            ]);
        },

        getEntity() {
            const criteria = new Criteria();
            criteria.addAssociation('media');

            return this.repository
                .get(this.$route.params.id, Shopware.Context.api, criteria)
                .then((entity) => {
                    this.item = entity;
                })
        },

        onClickSave() {
            this.isLoading = true;
            const titleSaveError = 'ERROR';
            const messageSaveError = 'ERROR MESSAGE';

            const titleSaveSuccess = 'SUCCESS';
            const messageSaveSuccess = 'SUCCESS MESSAGE';

            this.isSaveSuccessful = false;
            this.isLoading = true;

            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.getEntity();
                    this.isLoading = false;
                    this.processSuccess = true;
                    this.createNotificationSuccess({
                        tilte: titleSaveSuccess,
                        message: messageSaveSuccess
                    });
                }).catch(() => {
                    this.isLoading = false;
                    this.processSuccess = true;
                    this.createNotificationError({
                        title: titleSaveError,
                        message: messageSaveError
                    });
            });
        },

        onEditorChange(content) {
            this.item.description = content;
        },

        saveFinish() {
            this.processSuccess = false;
        },

        onUploadMedia(media) {
            this.setMediaItem({ targetId: media.targetId })
        },

        onDropMedia(media) {
            this.setMediaItem({ targetId: media.id })
        },

        setMediaItem({ targetId }) {
            this.mediaRepository.get(targetId).then((response) => {
                this.mediaItem = response;
            });
            this.item.mediaId = targetId;
        },

        onRemoveMedia() {
            this.mediaItem = null;
            this.item.mediaId = null;
        },

        onOpenMedia() {
            this.showMediaModal = true;
        },

        onMediaSelectionChange([mediaEntity]) {
            this.mediaItem = mediaEntity;
            this.item.mediaId = mediaEntity.id;
        },

        onChangeLanguage() {
            this.getEntity();
        },

        onTypeChange(value) {
            this.item.type = value;
        },
    }
})