export default {
    template: '#user_training_load_template',

    data() {
        return {
            error: '',
        }
    },

    methods: {

        loadUserTrainings() {

            // fetch(`${this.user_training_search_url}?${new URLSearchParams({user_query:this.user_query})}`)
            //     .then(response => {
            //         if (!response.ok) throw Error(response.statusText);
            //         return response.json();
            //     })
            //     .then(data => {
            //         this.user_search_results = data?.trainings ?? [];
            //         this.user_search_performed = true;
            //         this.user_searching = false;
            //     })
            //     .catch((error) => {
            //         this.user_searching = false;
            //         this.error = 'Error reading user search: ' + error.message;
            //     });
        },

    },
}