export default {
    template: '#user_training_search_template',

    data() {
        return {
            user_training_search_url: (typeof user_training_search_url === 'string') ? user_training_search_url : '/user_training_search',
            user_query: '',
            user_search_results: [],
            user_searching: false,
            user_search_performed: false,
            error: '',
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
            this.user_searching = false;
            this.user_search_performed = false;
        },

    },
}