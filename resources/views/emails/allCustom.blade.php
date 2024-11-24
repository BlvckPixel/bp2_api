    <!DOCTYPE html>
    <html>

    <head>
        <title>Blvckpixel: {{ $subjectLine }}</title>
        <style>
            a {
                text-decoration: none;
                display: inline-block; 
                padding: 10px 20px; 
                font-size: 16px; 
                color: #DD47F7 !important; 
                text-decoration: none;
                border-radius: 4px;
            }
            p {
                width: 100%;
                text-align: center;
                color: #666666; 
                font-size: 16px; 
                line-height: 1.5;
            }
        </style>
    </head>

    <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4;">
        <div style="max-width: 600px; margin: 40px auto; background-color: #d8e0dd; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h1 style="color: #333333; text-align: center; font-size: 24px; margin-top: 0; margin-bottom: 10px;">hello,[ {{ $name }} ]</h1>
            <div style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
            {!! $bodyContent !!}
            </div>

            <p style="text-align: center;">
            Thank you, <br>
            The [ BLVCKPIXEL ] Team
             </p>
        </div>
    </body>

    </html>