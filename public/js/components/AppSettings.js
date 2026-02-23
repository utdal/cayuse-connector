import { appSettingsStore } from "../stores/AppSettingsStore.js";

export default {
    template: '#app_settings_template',

    props: {
        initialCayuseEnvironment: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            appSettingsStore,
        }
    },

    mounted() {
        this.appSettingsStore.cayuseEnvironment = this.initialCayuseEnvironment;
    },

    methods: {

        isProd() {
            return this.appSettingsStore.isProd();
        },

    },
}