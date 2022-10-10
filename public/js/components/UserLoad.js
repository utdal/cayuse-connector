export default {
    template: '#user_load_template',

    data() {
        return {
            user_load_url: (typeof user_load_url === 'string') ? user_load_url : '/api/v1/user/load',
            user_file: null,
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
            return !!this.user_file && !this.getting_results;
        },

        readyToReset() {
            return (!!this.user_file || this.got_results || this.results.length) && !this.getting_results;
        }

    },

    methods: {

        fileSelected(event) {
            if (!event.target.files.length) return;
            this.user_file = event.target.files[0];
            this.got_results = false;
            this.getting_results = false;
            this.results = [];
        },

        loadUsers() {
            if (!(this.user_file instanceof File)) {
                this.showError('Please select a CSV file to upload');
                return;
            }

            this.got_results = false;
            this.getting_results = true;

            let form_data = new FormData();
            form_data.set('users', this.user_file, this.user_file.name);

            fetch(this.user_load_url, {
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
                    this.showError('Error uploading user: ' + error.message);
                });
        },

        clearResults(event) {
            const fileinput = event.target?.parentElement?.querySelector('input[type="file"]');
            if (fileinput instanceof HTMLInputElement) {
                fileinput.value = null;
            }
            this.got_results = false;
            this.getting_results = false;
            this.user_file = null;
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