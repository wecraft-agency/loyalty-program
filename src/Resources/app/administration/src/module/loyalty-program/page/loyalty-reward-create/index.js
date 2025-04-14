const { Component } = Shopware;
const utils = Shopware.Utils;

import template from './loyalty-reward-create.html.twig';

Component.extend('loyalty-reward-create', 'loyalty-reward-detail', {
    template,

    beforeRouteEnter(to, from, next) {
        if(to.name.includes('loyalty.program.rewards.create') && !to.params.id) {
            to.params.id = utils.createId();
            to.params.newItem = true;
        }

        next();
    },

    methods: {
        getEntity() {
            this.item = this.repository.create(Shopware.Context.api);
            this.item.active = true;
        },

        createdComponent() {
            if ( !Shopware.State.getters['context/isSystemDefaultLanguage'] ) {
                Shopware.State.commit('context/resetLanguageToDefault');
            }

            this.$super('createdComponent');
        },

        saveFinish() {
            this.isSaveSuccessful = false;
            this.$router.push({ name: 'loyalty.program.rewards.detail', params: { id: this.item.id } });
        },

        onClickSave() {
            this.isLoading = true;

            const titleSaveError = this.$tc('loyalty-program.rewards.general.savedErrorTitle');
            const messageSaveError = this.$tc('loyalty-program.rewards.general.savedErrorMessage');

            const titleSaveSuccess = this.$tc('loyalty-program.rewards.general.savedSuccessTitle');
            const messageSaveSuccess = this.$tc('loyalty-program.rewards.general.savedSuccessMessage');

            this.repository
                .save(this.item, Shopware.Context.api)
                .then(() => {
                    this.isLoading = false;
                    this.createNotificationSuccess({
                        title: titleSaveSuccess,
                        message: messageSaveSuccess
                    });

                    this.$router.push({ name: 'loyalty.program.rewards.detail', params: { id: this.item.id } });
                })
                .catch(() => {
                    this.isLoading = false;

                    this.createNotificationError({
                        title: titleSaveError,
                        message: messageSaveError
                    });

                })
        }
    }
})