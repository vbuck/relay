/**
 * Web UI app routes.
 *
 * Maps app-route aliases to web API endpoints.
 *
 * @requires  vue, webpack
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */

'use strict';

import Vue from '../node_modules/vue/dist/vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    backend: {
        'ping': '/service/status.json'
    },
    routes: [
    ]
});
