<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hamarosan lejár a diákigazolványod!</title>
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
        .highlight {
            color: #ffffff;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            padding: 10px;
            background-color: rgba(191, 64, 255, 0.1);
            border-radius: 8px;
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
        <h1 class="title">Szia {{ $user->first_name }}! 🎓</h1>
        
        <p class="text">A RoamPass rendszere szerint a diákigazolványod érvényessége <strong>5 nap múlva lejár</strong>.</p>
        
        <div class="highlight">
            Ne maradj le a kedvezményes edzésekről!
        </div>
        
        <p class="text">Kérlek, töltsd fel az új érvényesítő matricáról készült fotót a profilodban, hogy a rendszerünk hitelesíteni tudja, és továbbra is zökkenőmentesen használni tudd a bérletedet.</p>
        
        <a href="{{ route('profile.edit') }}" class="button">Profilom frissítése</a>
    </div>
</body>
</html>