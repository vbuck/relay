<!--
/**
 * Main content component. Loads child components based on the current view.
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */
-->
<template>
    <section class="main" v-bind:class="view_type">

    </section>
</template>

<script>
    export default {
        inject: ['network'],
        components: {
            'nav-menu': menuComponent
        },
        data: function () {
            return {
                view_type: '',
                data: {}
            }
        },
        created: function () {
            let uri = this.network.router.options.backend['app-info'] ?? false,
                error = 'Error retrieving data';

            if (!uri) {
                this.data.title = error;
            }

            this.network.request
                .get(uri)
                .then(response => (this.data.title = response.data.result.title ?? error));
        }
    };
</script>
