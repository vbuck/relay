<!--
/**
 * Header component. Renders app header content and main navigation.
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */
-->
<template>
    <header>
        <div class="row">
            <div class="col-lg-4">
                <div class="header__title" v-bind:data-loading="loading">
                    <img v-if="data.logo" v-bind:src="data.logo" v-bind:title="data.title" class="header__logo" />
                    <span v-else>{{ data.title }}</span>
                </div>
            </div>
            <div class="header__search col-lg-8">
                <form class="search" action="">
                    <input type="search" class="search__input" />
                </form>
            </div>
        </div>
        <nav class="header__menu" v-bind:data-loading="loading">
            <nav-menu v-bind:menu="data.menu"></nav-menu>
        </nav>
    </header>
</template>

<script>
    import menuComponent from './nav-menu.vue';

    export default {
        inject: ['network'],
        components: {
            'nav-menu': menuComponent
        },
        data: function () {
            return {
                loading: false,
                data: {
                    logo: '',
                    title: '',
                    menu: []
                }
            }
        },
        created: function () {
            this.loading = true;
            this.data.logo = '/' + require('../../dist/images/logo.png');

            let appInfoUri = this.network.router.options.backend['get-app-info'] ?? false,
                menuUri = this.network.router.options.backend['get-menu'] ?? false,
                error = 'Error retrieving data';

            if (!appInfoUri) {
                this.data.title = error;
                this.loading = false;
            } else {
                this.network.request
                    .get(appInfoUri)
                    .then(response => (this.data.title = response.data.result.title ?? error));
            }

            if (!menuUri) {
                this.data.menu = [{'error': error}];
                this.loading = false;
            } else {
                this.loading = true;
                this.network.request
                    .get(this.network.router.options.backend['get-menu'])
                    .then(response => (this.data.menu = response.data.result ?? [{'error': error}]))
                    .finally(() => (this.loading = false));
            }
        }
    };
</script>
