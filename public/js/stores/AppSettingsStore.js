import { reactive } from 'vue';

export const appSettingsStore = reactive({
    cayuseEnvironment: 'production',

    isProd() {
        return this.cayuseEnvironment === 'production';
    },

    isUAT() {
        return this.cayuseEnvironment === 'UAT';
    },

});