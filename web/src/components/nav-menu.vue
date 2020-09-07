<!--
/**
 * Footer component. Renders app footer content.
 *
 * @author    Rick Buczynski <richard.buczynski@gmail.com>
 * @copyright 2019 Rick Buczynski. All Rights Reserved.
 * @license   MIT
 */
-->
<template>
    <ul class="menu">
        <li class="menu__item" v-bind:class="{ 'menu__item--vertical': item.parent_id }" v-for="item in menu">
            <span class="error" v-if="item.error">{{ item.error }}</span>
            <a v-if="item.url" v-bind:href="item.url" v-on:focus="activate" v-on:blur="deactivate">{{ item.name }}</a>
            <nav-menu v-bind:menu="item.children" v-if="item.children && item.children.length"></nav-menu>
        </li>
    </ul>
</template>

<script>
    export default {
        name: 'nav-menu',
        props: {
            menu: Array
        },
        methods: {
            activate: function (event) {
                event.target.parentNode.querySelectorAll('.menu__item').forEach(function (item) {
                    item.className = item.className.replace(/(\s?)menu__item--active\s?/, '\\1');
                });

                event.target.className += ' menu__item--active';
            },
            deactivate: function (event) {
                event.target.className = event.target.className.replace(/\s?menu__item--active/, '');
            }
        }
    };
</script>
