import Vue from 'vue';
import {library} from '@fortawesome/fontawesome-svg-core'
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'

import {
    faEnvelope,
    faUser,
    faBuilding,
    faSignInAlt,
    faDolly,
    faBell,
    faCoins,
    faCog,
    faCartArrowDown,
    faBars,
    faSearch,
    faCheck,
    faAngleDown,
    faInfo,
    faTimesCircle,
    faDownload,
    faUpload,
    faEdit,
    faTrash,
    faSave,
    faFileImport,
    faFileDownload,
    faFileExcel,
    faTimes,
    faPlus,
    faClock,
    faCheckCircle,
    faMinusCircle,
    faChevronCircleRight,
    faFilter

} from '@fortawesome/free-solid-svg-icons'
// import { faUserCircle, faCheckCircle } from '@fortawesome/free-regular-svg-icons'

library.add(
    faUser,
    faBuilding,
    faEnvelope,
    faSignInAlt,
    faDolly,
    faBell,
    faCoins,
    faCog,
    faCartArrowDown,
    faSearch,
    faBars,
    faCheck,
    faAngleDown,
    faInfo,
    faTimesCircle,
    faDownload,
    faUpload,
    faEdit,
    faTrash,
    faSave,
    faFileImport,
    faFileDownload,
    faFileExcel,
    faTimes,
    faPlus,
    faClock,
    faCheckCircle,
    faMinusCircle,
    faChevronCircleRight,
    faFilter

);

Vue.component('font-awesome-icon', FontAwesomeIcon); // registered globally