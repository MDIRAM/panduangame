<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi OTP Anda</title>
    <style>
        body {
            background-color: #050812;
            color: #e9edf7;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
            text-align: center;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: #0f182f;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        h1 {
            color: #e9edf7;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #c4c9dd;
            font-size: 15px;
            line-height: 1.6;
        }
        .otp-code {
            display: inline-block;
            margin: 25px 0;
            padding: 15px 30px 15px 40px;
            background: linear-gradient(135deg, #ff4f48 0%, #ffaf6a 100%);
            color: #08101d;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 10px;
            border-radius: 999px;
            box-shadow: 0 4px 15px rgba(255, 79, 72, 0.3);
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verifikasi Email Anda</h1>
        <p>Terima kasih telah mendaftar di <strong>Walkthrough Game Hub</strong>. Silakan gunakan kode OTP di bawah ini untuk memverifikasi alamat email Anda:</p>
        <div class="otp-code">{{ $otpCode }}</div>
        <p>Kode ini hanya berlaku selama 10 menit. Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.</p>
        <div class="footer">
            &copy; {{ date('Y') }} Walkthrough Game Hub. All rights reserved.
        </div>
    </div>
</body>
</html>
