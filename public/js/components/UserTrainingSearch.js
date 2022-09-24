export default {
    template: '#user_training_search_template',

    data() {
        return {
            user_training_search_url: (typeof user_training_search_url === 'string') ? user_training_search_url : '/user_training_search',
            user_query: '',
            user_search_results: [],
            user_search_results_user: {},
            user_searching: false,
            user_search_performed: false,
            error: '',
        }
    },

    computed: {

        readyToSearch() {
            return !!this.user_query;
        },

        readyToReset() {
            return this.readyToSearch ||
                this.user_search_performed ||
                this.user_search_results.length ||
                this.user_search_results_user.length;
        },

        foundUser() {
            return this.user_search_performed &&
                this.user_search_results_user &&
                Object.keys(this.user_search_results_user).length !== 0;
        },

    },

    watch: {

        user_query(new_query, old_query) {
            this.user_search_performed = false;
            this.user_search_results = [];
            this.user_search_results_user = {};
        }

    },

    methods: {

        searchUserTrainings() {
            this.user_searching = true;
            this.user_search_performed = false;
            this.user_search_results = [];
            fetch(`${this.user_training_search_url}?${new URLSearchParams({user_query:this.user_query})}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.user_search_results = data?.trainings ?? [];
                    this.user_search_results_user = data?.user ?? [];
                    this.user_search_performed = true;
                    this.user_searching = false;
                })
                .catch((error) => {
                    this.user_searching = false;
                    this.error = 'Error reading user search: ' + error.message;
                });
        },

        clearUserSearchResults() {
            this.user_query = '';
            this.user_search_results = [];
            this.user_search_results_user = [];
            this.user_searching = false;
            this.user_search_performed = false;
        },

    },
}