export default {
    template: '#user_role_load_template',

    data() {
        return {
            role_search_url: (typeof role_search_url === 'string') ? role_search_url : '/api/v1/role',
            user_role_load_url: (typeof user_role_load_url === 'string') ? user_role_load_url : '/api/v1/user_role/load',
            role_file: null,
            role_file_type: 'roles',
            roles: [],
            getting_roles: false,
            got_roles: false,
            selected_roles: [],
            selected_unit_codes: {},
            selected_subunits: [],
            results: {},
            getting_results: false,
            got_results: false,
            show_report: false,
            error: '',
        }
    },

    computed: {

        selectUserRoles() {
            return this.role_file_type === 'users';
        },

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

    watch: {

        role_file_type(new_value, old_value) {
            if (new_value === 'users' && !this.got_roles) {
                this.getUserRoles();
            }
        },

    },

    methods: {

        fileSelected(event) {
            if (!event.target.files.length) return;
            this.role_file = event.target.files[0];
            this.got_results = false;
            this.getting_results = false;
            this.results = [];
        },

        getUserRoles() {
            this.getting_roles = true;
            fetch(this.role_search_url)
                .then(response => {
                    if (!response.ok) throw Error(response.statusText);
                    return response.json();
                })
                .then(data => {
                    this.roles = data?.roles ?? [];
                    this.got_roles = true;
                    this.getting_roles = false;
                })
                .catch((error) => {
                    this.getting_roles = false;
                    this.showError('Error fetching user roles: ' + error.message);
                });
        },

        setUnitCode(event) {
            let code = event.target.value;
            let role = event.target.dataset.role;

            if (code) {
                this.selected_unit_codes[role] = code;
            } else if (this.selected_unit_codes[role]) {
                delete this.selected_unit_codes[role];
            }
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

            if (this.selectUserRoles) {
                this.selected_roles.forEach(role => form_data.append('selected_roles[]', role));
                this.selected_subunits.forEach(role => form_data.append('selected_subunits[]', role));
                Object.keys(this.selected_unit_codes).forEach(role => form_data.append(`selected_unit_codes[${role}]`, this.selected_unit_codes[role]));
            }

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
                .catch(error => {
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