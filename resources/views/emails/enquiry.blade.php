<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Enquiry Notification</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #8A0707, #b50b0b);
            padding: 35px 25px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .content h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .greeting {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .enquiry-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 30px;
            margin: 30px 0;
            border-radius: 8px;
            border-left: 4px solid #8A0707;
        }

        .enquiry-card h3 {
            color: #8A0707;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: 600;
        }

        .enquiry-card p {
            font-size: 15px;
            line-height: 1.6;
            margin: 0;
            color: #555;
        }

        .highlight-box {
            background: #fef3f2;
            border: 1px solid #fed7d7;
            border-left: 4px solid #8A0707;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
            font-size: 15px;
            color: #2c3e50;
        }

        .btn {
            display: inline-block;
            padding: 15px 32px;
            margin-top: 30px;
            background: #8A0707;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .btn:hover {
            background: #b50b0b;
        }

        .footer {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .footer p {
            margin: 8px 0;
        }

        .footer a {
            color: #8A0707;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .tip {
            margin-top: 25px;
            font-size: 14px;
            color: #6c757d;
            font-style: italic;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .container {
                border-radius: 8px;
            }

            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .content {
                padding: 30px 20px;
            }

            .btn {
                padding: 12px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>New Order Enquiry</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Hello Seller!</h2>
            <p class="greeting">You have received a new order enquiry on your product.</p>

            <div class="enquiry-card">
                <h3>📋 Customer Enquiry Received</h3>
                <p>
                    A potential customer has submitted an enquiry for one of your products.
                    Please review the details and respond promptly to convert this enquiry into a sale.
                </p>
            </div>

            <div class="highlight-box">
                <strong>Action Required:</strong> Please log in to your seller dashboard to view the complete enquiry
                details, customer information, and product specifications. Quick responses help improve your conversion
                rate.
            </div>

            <a href="{{ url('/seller/orders') }}" class="btn">
                View Enquiry Details
            </a>

            <p class="tip">
                💡 Tip: Responding within 24 hours significantly increases your chances of securing the order.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>This is an automated notification from Your Store</strong></p>
            <p>
                <a href="{{ url('/') }}">Visit Website</a> •
                <a href="{{ url('/seller/help') }}">Help Center</a> •
                <a href="{{ url('/seller/settings') }}">Account Settings</a>
            </p>
            <p style="margin-top: 15px;">
                © {{ date('Y') }} Your Store. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
