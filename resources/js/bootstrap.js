import axios from 'axios';
import Conveyor from "socket-conveyor-client";
import {marked} from "marked";

window.axios = axios;

window.Conveyor = Conveyor;

window.marked = marked;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

// import './echo';
