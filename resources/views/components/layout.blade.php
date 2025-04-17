<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        main{
            min-height: 88vh;
            padding: 6px 22px;
        }
    </style>
</head>
<body>

    @if(!isset($showHeader) || $showHeader)
        <x-header/>
    @endif

    <main>
        {{ $slot }}
    </main>

    @if(!isset($showFooter) || $showFooter)
        <x-footer/>
    @endif

</body>
</html>