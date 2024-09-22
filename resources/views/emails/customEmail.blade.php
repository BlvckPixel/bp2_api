<!-- resources/views/emails/customEmail.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blvckpixel: {{ $subjectLine }}</title>
</head>
<body>
    <h1 style="color: #333333; font-size: 20px; margin-top: 0;">Hello, {{ $user->name }}!</h1>
    <p style="color: #666666; font-size: 16px; line-height: 1.5;">{!! $bodyContent !!}</p> <!-- Allow HTML content in the body -->
    <p >Best Regards 😊!</p>
</body>
</html>
