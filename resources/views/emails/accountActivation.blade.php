    <!DOCTYPE html>
    <html>

    <head>
        <title>Blvckpixel: Account Activation</title>
    </head>

    <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h1 style="color: #333333; text-align: center; font-size: 24px; margin-top: 0;">Welcome, {{ $user->name }}</h1>
            <p style="color: #666666; font-size: 16px; line-height: 1.5;">
                Thank you for registering. Please click the link below to activate your account:
            </p>
            <p style="text-align: center;">
                <a href="{{ env('FRONTEND_URL') }}/activate-account/{{ $token }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #000000; text-decoration: none; border-radius: 4px;">
                    Activate Account
                </a>
            </p>
        </div>
    </body>

    </html>