import { appSettingsStore } from "../stores/AppSettingsStore.js";

export default {
    template: '#job_report_template',

    props: {
        type: {
            type: String,
            required: true,
        },
        jobId: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            appSettingsStore,
            job_report_url: (typeof job_report_url === 'string') ? job_report_url : '/api/v1/job/report',
            report: {},
            getting_report: false,
            got_report: false,
            error: '',
        }
    },

    computed: {

        reportHtml() {
            return this.report?.html ?? '';
        },

        readyToCheck() {
            return !this.getting_report;
        },

    },

    methods: {

        getJobReport() {
            if (this.getting_report) return;
            this.getting_report = true;
            this.got_report = false;
            this.report = {};
            const search_params = new URLSearchParams({
                type: this.type,
                jobId: this.jobId,
                environment: this.appSettingsStore.cayuseEnvironment,
            });

            fetch(`${this.job_report_url}?${search_params}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.report = data ?? {};
                    this.got_report = true;
                    this.getting_report = false;
                })
                .catch((error) => {
                    this.getting_report = false;
                    this.error = 'Error reading job report: ' + error.message;
                });
        },

        clearJobReportResults() {
            this.report = {};
            this.getting_report = false;
            this.got_report = false;
        },

    },

}