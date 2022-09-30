export default {
    template: '#user_training_search_template',

    data() {
        return {
            user_training_search_url: (typeof user_training_search_url === 'string') ? user_training_search_url : '/user_training_search',
            first_name: '',
            last_name: '',
            employee_id: '',
            user_search_results: [],
            user_searching: false,
            user_search_performed: false,
            error: '',
        }
    },

    computed: {

        user_query() {
            return this.first_name || this.last_name || this.employee_id;
        },

        readyToSearch() {
            return !!this.user_query && !this.user_searching;
        },

        readyToReset() {
            return this.readyToSearch ||
                this.user_search_performed ||
                this.user_search_results.length;
        },

    },

    watch: {

        first_name(new_query, old_query) {
            this.clearSearchPerformed();
        },

        last_name(new_query, old_query) {
            this.clearSearchPerformed();
        },

        employee_id(new_query, old_query) {
            this.clearSearchPerformed();
        },

    },

    methods: {

        searchUserTrainings() {
            this.user_searching = true;
            this.user_search_performed = false;
            this.user_search_results = [];
            const search_params = new URLSearchParams({
                id: this.employee_id,
                first: this.first_name,
                last: this.last_name,
            });

            fetch(`${this.user_training_search_url}?${search_params}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.user_search_results = data?.people ?? [];
                    this.user_search_performed = true;
                    this.user_searching = false;
                })
                .catch((error) => {
                    this.user_searching = false;
                    this.error = 'Error reading user search: ' + error.message;
                });
        },

        clearSearchPerformed() {
            this.user_search_performed = false;
            this.user_search_results = [];
        },

        clearUserSearchResults() {
            this.first_name = '';
            this.last_name = '';
            this.employee_id = '';
            this.user_search_results = [];
            this.user_searching = false;
            this.user_search_performed = false;
        },

    },
}