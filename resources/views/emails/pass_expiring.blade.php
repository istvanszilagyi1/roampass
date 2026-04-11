<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hamarosan lejár a bérleted!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0a0c1e;
            color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #13162b;
            border: 1px solid #bf40ff;
            border-radius: 15px;
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            text-align: center;
        }
        .title {
            color: #bf40ff;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .text {
            color: #cccccc;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .number-box {
            font-size: 36px;
            font-weight: bold;
            color: #ffffff;
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(191, 64, 255, 0.1);
            border: 1px dashed #bf40ff;
            border-radius: 10px;
            display: inline-block;
        }
        .button {
            background-color: #bf40ff;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Szia {{ $pass->user->first_name }}! ⏳</h1>
        
        <p class="text">Figyelem! A jelenlegi RoamPass bérleted <strong>5 nap múlva ({{ \Carbon\Carbon::parse($pass->expires_at)->format('Y.m.d.') }})</strong> érvénytelenné válik.</p>
        
        <p class="text">Ebből a bérletből még ennyi alkalmad maradt:</p>
        
        <div class="number-box">
            {{ $pass->remaining_uses }} alkalom
        </div>
        
        <p class="text">Használd ki gyorsan a maradékot, vagy gondoskodj a hosszabbításról időben!</p>
        
        <a href="{{ route('passes.create') }}" class="button">Új bérlet vásárlása</a>
    </div>
</body>
</html>