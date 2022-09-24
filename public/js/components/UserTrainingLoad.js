import { ndjsonStream } from '../ndjsonstream.js';

export default {
    template: '#user_training_load_template',

    data() {
        return {
            user_training_types_url: (typeof user_training_types_url === 'string') ? user_training_types_url : '/user_training_types',
            user_training_load_url: (typeof user_training_load_url === 'string') ? user_training_load_url : '/user_training/load',
            training_types: [],
            training_type_id: '',
            training_file: null,
            results: [],
            getting_results: false,
            got_results: false,
            getting_training_types: false,
            got_training_types: false,
            error: '',
        }
    },

    computed: {

        readyToLoad() {
            return !!this.training_file && !!this.training_type_id;
        },

    },

    mounted() {
        this.getUserTrainingTypes();
    },

    methods: {

        getUserTrainingTypes() {
            this.getting_training_types = true;
            fetch(this.user_training_types_url)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.training_types = data?.training_types ?? [];
                    this.got_training_types = true;
                    this.getting_training_types = false;
                })
                .catch((error) => {
                    this.getting_training_types = false;
                    this.showError('Error fetching training types: ' + error.message);
                });
        },

        fileSelected(event) {
            if (!event.target.files.length) return;
            this.training_file = event.target.files[0];
            this.results = [];
        },

        loadUserTrainings() {
            if (!(this.training_file instanceof File)) {
                this.showError('Please select a CSV file to upload');
                return;
            }

            this.got_results = false;
            this.getting_results = true;

            let form_data = new FormData();
            form_data.set('training', this.training_file, this.training_file.name);
            form_data.set('training_type', this.training_type_id);

            fetch(this.user_training_load_url, {
                body: form_data,
                method: 'post',
            })
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return ndjsonStream(response.body);
                })
                .then(stream => {
                    this.readResults(stream.getReader());
                })
                .catch((error) => {
                    this.showError('Error fetching training types: ' + error.message);
                });
        },

        readResults(stream_reader) {
            stream_reader.read().then(({ value, done }) => {
                if (done) {
                    this.got_results = true;
                    return;
                }
                if (value) {
                    this.results.push(value);
                }
                this.readResults(stream_reader);
            });
        },

        clearResults(event) {
            const fileinput = event.target?.parentElement?.querySelector('input[type="file"]');
            if (fileinput instanceof HTMLInputElement) {
                fileinput.value = null;
            }
            this.got_results = false;
            this.getting_results = false;
            this.training_type_id = '';
            this.training_file = null;
            this.results = [];
            this.error = '';
        },

        showError(message) {
            this.error = message;
            this.results.push({status: 'error', message: message});
        },

    },
}