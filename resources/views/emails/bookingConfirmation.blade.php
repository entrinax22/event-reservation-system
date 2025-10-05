<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmation - Big City Pro</title>
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
            <h2>Big City Pro - Booking Confirmation</h2>
        </div>
        <div class="email-body">
            <p>Hello <strong>{{ $booking->user->name ?? 'Valued Customer' }}</strong>,</p>

            <p>Thank you for choosing <strong>Big City Pro</strong>! Your booking has been successfully received.</p>

            <h3>Booking Details</h3>
            <table class="details-table">
                <tr>
                    <th>Booking Reference:</th>
                    <td>#{{ $booking->reserved_event_id }}</td>
                </tr>
                <tr>
                    <th>Event:</th>
                    <td>{{ $booking->event->event_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Start Date:</th>
                    <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <th>End Date:</th>
                    <td>{{ \Carbon\Carbon::parse($booking->event_end_date)->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td>{{ ucfirst($booking->status) }}</td>
                </tr>
                <tr>
                    <th>Notes:</th>
                    <td>{{ $booking->event_notes ?? 'None' }}</td>
                </tr>
                <tr>
                    <th>Total Cost:</th>
                    <td class="total">₱{{ number_format($booking->total_cost, 2) }}</td>
                </tr>
                @if(!empty($booking->downpayment_amount))
                <tr>
                    <th>Downpayment:</th>
                    <td>₱{{ number_format($booking->downpayment_amount, 2) }}</td>
                </tr>
                @endif
            </table>

            @if($booking->materials && count($booking->materials) > 0)
                <h3 style="margin-top:20px;">Reserved Materials</h3>
                <ul>
                    @foreach($booking->materials as $material)
                        <li>{{ $material->material->material_name ?? 'Unknown Material' }}</li>
                    @endforeach
                </ul>
            @endif

            <p style="margin-top:25px;">We look forward to serving you on your scheduled event!</p>

            <p>Best regards,<br>
            <strong>Big City Pro Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Big City Pro. All rights reserved.
        </div>
    </div>
</body>
</html>
