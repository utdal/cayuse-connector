export default {
    template: '#unit_search_template',

    data() {
        return {
            unit_search_url: (typeof unit_search_url === 'string') ? unit_search_url : '/api/v1/unit',
            unit_name: '',
            unit_code: '',
            unit_search_results: [],
            unit_searching: false,
            unit_search_performed: false,
            error: '',
        }
    },

    computed: {

        unit_query() {
            return this.unit_name || this.unit_code;
        },

        readyToSearch() {
            return !!this.unit_query && !this.user_searching;
        },

        readyToReset() {
            return this.readyToSearch || this.unit_search_performed || this.unit_search_results.length;
        }

    },

    watch: {

        unit_name(new_query, old_query) {
            this.clearSearchPerformed();
        },

        unit_code(new_query, old_query) {
            this.clearSearchPerformed();
        },

    },

    methods: {

        searchUnits() {
            this.unit_searching = true;
            this.unit_search_performed = false;
            this.unit_search_results = [];
            fetch(`${this.unit_search_url}?${new URLSearchParams({name:this.unit_name, code:this.unit_code})}`)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.unit_search_results = data?.units ?? [];
                    this.unit_search_performed = true;
                    this.unit_searching = false;
                })
                .catch((error) => {
                    this.unit_searching = false;
                    this.error = 'Error reading unit search: ' + error.message;
                });
        },

        clearSearchPerformed() {
            this.unit_search_performed = false;
            this.unit_search_results = [];
        },

        clearUnitSearchResults() {
            this.unit_name = '';
            this.unit_code = '';
            this.unit_searching = false;
            this.unit_search_results = [];
            this.unit_search_performed = false;
        },

    },
}