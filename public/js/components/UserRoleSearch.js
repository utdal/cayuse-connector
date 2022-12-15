export default {
    template: '#user_role_search_template',

    data() {
        return {
            user_role_search_url: (typeof user_role_search_url === 'string') ? user_role_search_url : '/api/v1/user_role',
            first_name: '',
            last_name: '',
            username: '',
            user_search_results: [],
            user_searching: false,
            user_search_performed: false,
            error: '',
        }
    },

    computed: {

        user_query() {
            return this.first_name || this.last_name || this.username;
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

        username(new_query, old_query) {
            this.clearSearchPerformed();
        },

    },

    methods: {

        searchUserRoles() {
            this.user_searching = true;
            this.user_search_performed = false;
            this.user_search_results = [];
            const search_params = new URLSearchParams({
                id: this.username,
                first: this.first_name,
                last: this.last_name,
            });

            fetch(`${this.user_role_search_url}?${search_params}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.user_search_results = data?.users ?? [];
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
            this.username = '';
            this.user_search_results = [];
            this.user_searching = false;
            this.user_search_performed = false;
        },

    },
}