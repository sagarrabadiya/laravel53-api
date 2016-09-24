<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="/css/app.css?time={{time()}}">
        <base href="/" />
    </head>
    <body>
        <router-view transition="viewSlide" transition-mode="out-in"></router-view>
    </body>
    <script>
        window.Laravel = {!!  json_encode([
                'csrfToken' => csrf_token(),
        ]) !!}
    </script>
    <script type="text/javascript" src="/js/vendor.js"></script>
    <script type="text/javascript" src="/js/app.js?time={{time()}}"></script>
</html>
