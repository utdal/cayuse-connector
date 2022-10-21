import { createApp } from 'vue';
import { far } from "@fortawesome/free-regular-svg-icons"
import { fas } from "@fortawesome/free-solid-svg-icons"
import { fab } from "@fortawesome/free-brands-svg-icons"
import { library } from "@fortawesome/fontawesome-svg-core"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import JobStatus from "./components/JobStatus.js";
import JobReport from "./components/JobReport.js";
import UnitSearch from "./components/UnitSearch.js";
import UserSearch from "./components/UserSearch.js";
import UserAffiliationSearch from "./components/UserAffiliationSearch.js";
import UserTrainingSearch from "./components/UserTrainingSearch.js";
import UserTrainingLoad from "./components/UserTrainingLoad.js";
import UserLoad from "./components/UserLoad.js";
import UserAffiliationLoad from "./components/UserAffiliationLoad.js";

library.add(far, fas, fab)

const app = createApp({

    components: {
        UnitSearch,
        UserSearch,
        UserAffiliationSearch,
        UserTrainingSearch,
        UserTrainingLoad,
        UserLoad,
        UserAffiliationLoad,
    }

});

app.component('icon', FontAwesomeIcon);
app.component('job-status', JobStatus);
app.component('job-report', JobReport);

app.mount('#app');