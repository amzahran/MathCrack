<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('l.maintenance')</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: ltr;
        }
        .maintenance-container {
            text-align: center;
            padding: 2rem;
        }
        .icon {
            font-size: 5rem;
            color: #3490dc;
            margin-bottom: 1rem;
        }
        h1 {
            color: #2d3748;
            margin-bottom: 1rem;
        }
        p {
            color: #4a5568;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="icon">🔧</div>
        <h1>@lang('l.maintenance')</h1>
        <p>@lang('l.maintenance_message')</p>
        <p>@lang('l.maintenance_message_2')</p>
    </div>
</body>
</html>
