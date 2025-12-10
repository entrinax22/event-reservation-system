<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Updated - Big City Pro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #03d8a0, #04746a, #0b3b36);
            color: #d8ffef;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 650px;
            background: rgba(0, 0, 0, 0.85);
            margin: 40px auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0 25px rgba(0, 245, 160, 0.3);
            border: 1px solid rgba(0, 245, 160, 0.2);
        }
        .email-header {
            background: linear-gradient(to right, #00f5a0, #00b2ff);
            color: #000;
            padding: 25px;
            text-align: center;
        }
        .email-header img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 30px;
            color: #d8ffef;
        }
        h3 {
            color: #00f5a0;
            border-bottom: 1px solid rgba(0, 245, 160, 0.3);
            padding-bottom: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .details-table th, .details-table td {
            text-align: left;
            padding: 8px 0;
        }
        .details-table th {
            width: 40%;
            color: #7fbfb0;
        }
        .details-table td {
            width: 60%;
            color: #d8ffef;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
            color: #00f5a0;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        ul li {
            background: rgba(0, 245, 160, 0.1);
            margin: 5px 0;
            padding: 8px 10px;
            border-radius: 6px;
        }
        .footer {
            background: rgba(0, 0, 0, 0.9);
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #7fbfb0;
            border-top: 1px solid rgba(0, 245, 160, 0.2);
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/logo.png')) : asset('images/logo.png') }}" alt="Big City Pro Logo" style="width:70px;height:70px;border-radius:50%;margin-bottom:10px;">
            <h2>Big City Pro - OTP CODE</h2>
        </div>
        <div class="email-body">
            <h3>Your One-Time Password (OTP)</h3>
            <p>Dear User,</p>
            <p>We received a request to generate a One-Time Password (OTP) for your account. Please use the OTP below to proceed with your action. This OTP is valid for the next 10 minutes.</p>
            <h2 style="text-align: center; background: rgba(0, 245, 160, 0.1); padding: 15px; border-radius: 8px; color: #00f5a0;">{{ $otp }}</h2>
            <p>If you did not request this OTP, please ignore this email. For security reasons, do not share this OTP with anyone.</p>
            <p>Thank you for using Big City Pro!</p>
            <p>Best regards,<br>The Big City Pro Team</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Big City Pro. All rights reserved.
        </div>
    </div>
</body>
</html>
