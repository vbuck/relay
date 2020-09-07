/**
 * Web UI app entry point.
 *
 * Manages Vue single-file component registration process. The manifest defines the component list and order, which is
 * converted to a component schedule to be bundled by webpack. Use `grunt watch` while building.
 *
 * @requires  vue, webpack
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */

'use strict';

import Vue from '../node_modules/vue/dist/vue';
import Axios from 'axios-cache-adapter';
import LocalForage from 'localforage';
import Router from './routes';

const manifest = [
    './components/app-header.vue',
    './components/app-footer.vue',
    './components/app-notifier.vue'
];

Vue.config.productionTip = false;

const Request = Axios.setup({
    cache: {
        //maxAge: 15 * 60 * 1000,
        maxAge: 5000,
        store: LocalForage.createInstance({
            driver: [
                LocalForage.INDEXEDDB,
                LocalForage.LOCALSTORAGE
            ],
            name: 'app-cache'
        })
    }
});

(function (vue, request, router) {
    let componentSchedule = {};

    for (let index in manifest) {
        let path = manifest[index],
            name = path.split('/').pop().replace(/\..*$/, ''),
            component = require(`${path}`),
            config = component.default || {};

        componentSchedule[name] = vue.component(name, config);
    }

    new vue({
        el: '#app-container',
        router: router,
        components: componentSchedule,
        provide: {
            network: {
                request: request,
                router: router
            }
        }
    });
})(Vue, Request, Router);
