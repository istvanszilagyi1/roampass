<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hamarosan elfogy a bérleted!</title>
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
        }
        .number {
            font-size: 48px;
            font-weight: bold;
            color: #ffffff;
            margin: 20px 0;
        }
        .text {
            color: #cccccc;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Szia {{ $user->first_name }}!</h1>
        
        <p class="text">Látjuk, hogy keményen edzel! Viszont a jelenlegi RoamPass bérletedből már csak kevés alkalom maradt hátra:</p>
        
        <div class="number">{{ $remainingUses }} alkalom</div>
        
        <p class="text">Gondoskodj az utánpótlásról időben, nehogy a következő edzésnél a kapuban kelljen bérletet venned!</p>
        
        <a href="{{ route('passes.create') }}" class="button">Új bérlet vásárlása</a>
    </div>
</body>
</html>