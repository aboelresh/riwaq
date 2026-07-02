<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .code-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 10px;
            padding: 30px;
            border-radius: 12px;
            margin: 30px 0;
            font-family: 'Courier New', monospace;
        }
        .message {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            color: #856404;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Code Master</h1>
            <p>Your Learning Journey Starts Here</p>
        </div>
        
        <div class="content">
            <h2>Welcome, {{ $user->name }}! 👋</h2>
            
            <p class="message">
                Thank you for joining <strong>Code Master</strong>! 
                To activate your account and start learning, please verify your email address.
            </p>
            
            <p class="message">
                Enter this verification code in the application:
            </p>
            
            <div class="code-box">
                {{ $code }}
            </div>
            
            <div class="warning">
                ⏰ <strong>Important:</strong> This code expires in 15 minutes
            </div>
            
            <p class="message">
                If you didn't create an account, you can safely ignore this email.
            </p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Code Master - Learning Platform</p>
            <p>Happy Learning! 🚀</p>
        </div>
    </div>
</body>
</html>