<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center; /* Center-align the contents of the container */
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            font-size: 16px;
            color: #666;
            text-align: center;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            background-color: #f0f0f0;
            padding: 10px 20px; /* Add horizontal padding for better spacing */
            border-radius: 5px;
            display: inline-block; /* Keep it inline but centered */
            margin: 20px 0; /* Center it by adjusting margin (top and bottom only) */
        }
    </style>
</head>
<body>
<div class="email-container">
    <h1>Password Reset OTP</h1>
    <p>Your password reset OTP is:</p>
    <p class="otp">{{$resetPasswordToken}}</p>
    <p>If you did not request this, please ignore this email.</p>
</div>
</body>
</html>
