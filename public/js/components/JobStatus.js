export default {
    template: '#job_status_template',

    props: {
        type: {
            type: String,
            required: true,
        },
        jobId: {
            type: String,
            required: true,
        },
        jobCount: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            job_status_url: (typeof job_status_url === 'string') ? job_status_url : '/api/v1/job/status',
            job_status: '',
            job_completed: false,
            job_successes: 0,
            job_errors: 0,
            job_total_row_count: 0,
            auto_check: true,
            check_every: 15,
            checking_status: false,
            checked_status: false,
            timer: null,
            error: '',
        }
    },

    computed: {

        successPercent() {
            if (this.jobCount <= 0) {
                return 0;
            }

            return Math.round(this.job_successes / this.jobCount * 100);
        },

        errorPercent() {
            if (this.jobCount <= 0) {
                return 0;
            }

            return Math.round(this.job_errors / this.jobCount * 100);
        },

        readyToCheck() {
            return !this.checking_status && !this.job_completed && !this.auto_check;
        },

    },

    emits: {
        'status-updated': null,
        'job-completed': null,
    },

    mounted() {
        this.startAutoChecking();
    },

    watch: {

        job_completed(new_value, old_value) {
            if (!!new_value) {
                this.auto_check = false;
                this.stopAutoChecking();
                this.$emit('job-completed', new_value);
            }
        },

        job_status(new_value, old_value) {
            this.$emit('status-updated', new_value);
        },

        auto_check(new_value, old_value) {
            this.restartAutoChecking();
        },

        check_every(new_value, old_value) {
            this.restartAutoChecking();
        },

    },

    methods: {

        getJobStatus() {
            if (this.checking_status) return;
            this.checking_status = true;
            this.checked_status = false;
            this.status = {};
            fetch(`${this.job_status_url}?${new URLSearchParams({type: this.type, jobId: this.jobId})}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.job_status = data?.status ?? '';
                    this.job_completed = data?.completed ?? false;
                    this.job_successes = data?.successes ?? 0;
                    this.job_errors = data?.errors ?? 0;
                    this.job_total_row_count = data?.totalRowCount ?? 0;
                    this.checked_status = true;
                    this.checking_status = false;
                })
                .catch((error) => {
                    this.checking_status = false;
                    this.error = 'Error reading job status: ' + error.message;
                });
        },

        startAutoChecking() {
            if (this.auto_check && !this.job_completed) {
                this.timer = setInterval(() => {
                    this.getJobStatus();
                }, this.check_every * 1000);
            }
        },

        stopAutoChecking() {
            clearInterval(this.timer);
        },

        restartAutoChecking() {
            this.stopAutoChecking();
            this.startAutoChecking();
        },

        clearCheckPerformed() {
            this.checked_status = false;
            this.status = '';
        },

        clearJobStatusResults() {
            this.job_status = '';
            this.job_completed = false;
            this.job_successes = 0;
            this.job_errors = 0;
            this.job_total_row_count = 0;
            this.checking_status = false;
            this.checked_status = false;
        },

    },

    beforeUnmount() {
        this.stopAutoChecking();
    },
}