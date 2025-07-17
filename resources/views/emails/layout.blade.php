<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Oja Ewa')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 0;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        .button:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 30px 0;
        }
        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
            }
            .content {
                padding: 20px;
            }
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="https://example.com/logo.png" alt="Oja Ewa Logo" class="logo">
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <div class="social-links">
                <a href="#">Facebook</a>
                <a href="#">Twitter</a>
                <a href="#">Instagram</a>
            </div>
            <p>&copy; {{ date('Y') }} Oja Ewa. All rights reserved.</p>
            <p>You're receiving this email because you have an account with us.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
