import { createApp } from 'vue';
import { far } from "@fortawesome/free-regular-svg-icons"
import { fas } from "@fortawesome/free-solid-svg-icons"
import { fab } from "@fortawesome/free-brands-svg-icons"
import { library } from "@fortawesome/fontawesome-svg-core"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import UserSearch from "./components/UserSearch.js";
import UserTrainingSearch from "./components/UserTrainingSearch.js";
import UserTrainingLoad from "./components/UserTrainingLoad.js";

library.add(far, fas, fab)

const app = createApp({

    components: {
        UserSearch,
        UserTrainingSearch,
        UserTrainingLoad,
    }

});

app.component('icon', FontAwesomeIcon);
app.mount('#app');