export default {
    template: '#user_role_load_template',

    data() {
        return {
            user_role_load_url: (typeof user_role_load_url === 'string') ? user_role_load_url : '/api/v1/user_role/load',
            role_file: null,
            results: {},
            getting_results: false,
            got_results: false,
            show_report: false,
            error: '',
        }
    },

    computed: {

        jobId() {
            return this.results?.jobId ?? '';
        },

        jobCount() {
            return this.results?.count ?? 0;
        },

        readyToLoad() {
            return !!this.role_file && !this.getting_results;
        },

        readyToReset() {
            return (!!this.role_file || this.got_results || this.results.length) && !this.getting_results;
        }

    },

    methods: {

        fileSelected(event) {
            if (!event.target.files.length) return;
            this.role_file = event.target.files[0];
            this.got_results = false;
            this.getting_results = false;
            this.results = [];
        },

        loadRoles() {
            if (!(this.role_file instanceof File)) {
                this.showError('Please select a CSV file to upload');
                return;
            }

            this.got_results = false;
            this.getting_results = true;

            let form_data = new FormData();
            form_data.set('roles', this.role_file, this.role_file.name);

            fetch(this.user_role_load_url, {
                body: form_data,
                method: 'post',
            })
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.results = data;
                    console.log(data);
                    this.getting_results = false;
                    this.got_results = true;
                })
                .catch((error) => {
                    this.getting_results = false;
                    this.showError('Error uploading roles: ' + error.message);
                });
        },

        clearResults(event) {
            const fileinput = event.target?.parentElement?.querySelector('input[type="file"]');
            if (fileinput instanceof HTMLInputElement) {
                fileinput.value = null;
            }
            this.got_results = false;
            this.getting_results = false;
            this.role_file = null;
            this.results = {};
            this.error = '';
        },

        showReport() {
            this.show_report = true;
        },

        showError(message) {
            this.error = message;
        },

    },
}