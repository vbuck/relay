<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title><% app.title %></title>
        <link rel="stylesheet" type="text/css" href="/dist/main.css" />
    </head>
    <body>
        <div id="app-container" class="<?php echo \uniqid('generated-class-'); ?>">
            <app-header></app-header>
            <router-view></router-view>
            <app-footer></app-footer>
            <app-notifier></app-notifier>
        </div>
        <script type="application/javascript" src="/dist/main.js"></script>
    </body>
</html>