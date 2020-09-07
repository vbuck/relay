<!--
/**
 * Notification component. Provides app-wide alerts.
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */
-->
<template>
    <div class="notifier" role="dialog" v-bind:class="{ hidden: 'hidden' }">
        <p>{{ content }}</p>
        <button v-bind:onclick="close">{{ action_label }}</button>
    </div>
</template>

<script>
    export default {
        inject: ['network'],
        data: function () {
            return {
                hidden: false,
                content: 'Test content',
                action_label: 'OK',
            }
        },
        methods: {
            close: function () {
                this.data.hidden = true;
                this.data.content = '';
            }
        }
    };
</script>
