<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Submission</title>
    <style>
        body {
            font-family: 'Inter', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f6f6;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f6f6f6;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            font-family: sans-serif;
            color: #4a4a4a;
        }
        .header {
            padding: 40px 30px;
            text-align: left;
            border-bottom: 1px solid #eeeeee;
        }
        .content {
            padding: 40px 30px;
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #9b9b9b;
        }
        h2 {
            margin: 0;
            color: #000000;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9b9b9b;
            margin-bottom: 4px;
            display: block;
        }
        .value {
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .message-box {
            background-color: #fafafa;
            border-radius: 4px;
            padding: 20px;
            border: 1px solid #eeeeee;
            white-space: pre-wrap;
        }
        .divider {
            height: 1px;
            background-color: #eeeeee;
            margin: 24px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main">
            <tr>
                <td class="header">
                    <h2>New Submission</h2>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <span class="label">Sender Name</span>
                    <div class="value">{{ $name }}</div>

                    <span class="label">Email Address</span>
                    <div class="value">{{ $email }}</div>

                    <span class="label">Phone Number</span>
                    <div class="value">{{ $phone }}</div>

                    <span class="label">Subject</span>
                    <div class="value">{{ $subject }}</div>

                    <div class="divider"></div>

                    <span class="label">Message</span>
                    <div class="message-box value">{{ $messageBody }}</div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
