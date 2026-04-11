<!DOCTYPE html>
<html>
<head>
    <title>{{ $emailSubject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="background-color: #f4f4f4; padding: 20px;">
        <div style="background-color: #fff; padding: 20px; border-radius: 8px;">
            {!! $emailContent !!}
        </div>
        <p style="font-size: 12px; color: #888; text-align: center; margin-top: 20px;">
            Ezt a levelet a RoamPass rendszer küldte. <br>
            Ha nem szeretnél több levelet kapni, állítsd át a profilodban.
        </p>
    </div>
</body>
</html>