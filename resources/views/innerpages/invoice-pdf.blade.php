<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $data['invoice_number'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        
        .invoice-header h1 {
            color: #007bff;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .invoice-header h2 {
            color: #28a745;
            font-size: 1.5rem;
            font-weight: normal;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .invoice-details, .payment-details {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            margin: 0 10px;
            border-radius: 8px;
        }
        
        .invoice-details h3, .payment-details h3 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        
        .detail-value {
            color: #333;
        }
        
        .payment-summary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        
        .payment-summary h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .payment-summary .amount {
            font-size: 3rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .crypto-details {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .crypto-details h4 {
            color: #0c5460;
            margin-bottom: 15px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #666;
        }
        
        .status-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .invoice-container {
                max-width: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1>Payment Invoice</h1>
            <h2>Payment Successful</h2>
        </div>
        
        <!-- Invoice Information -->
        <div class="invoice-info">
            <div class="invoice-details">
                <h3>Invoice Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Invoice #:</span>
                    <span class="detail-value">{{ $data['invoice_number'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">{{ $data['order']->updated_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value"><span class="status-badge">Paid</span></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">{{ ucfirst($data['order']->payment_method ?? 'Unknown') }}</span>
                </div>
            </div>
            
            <div class="payment-details">
                <h3>Payment Information</h3>
                @if($data['order']->isCryptoPayment())
                    <div class="detail-row">
                        <span class="detail-label">Cryptocurrency:</span>
                        <span class="detail-value">{{ strtoupper($data['order']->crypto_currency ?? 'N/A') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value">{{ number_format($data['order']->pay_amount ?? $data['order']->crypto_amount, 8) }} {{ strtoupper($data['order']->pay_currency ?? $data['order']->crypto_currency) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Network:</span>
                        <span class="detail-value">{{ ucfirst($data['order']->network ?? 'Blockchain') }}</span>
                    </div>
                    @if($data['order']->nowpayments_payment_id)
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value">{{ $data['order']->nowpayments_payment_id }}</span>
                    </div>
                    @endif
                @else
                    <div class="detail-row">
                        <span class="detail-label">Amount:</span>
                        <span class="detail-value">${{ number_format($data['order']->amount, 2) }}</span>
                    </div>
                    @if($data['order']->nowpayments_payment_id)
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value">{{ $data['order']->nowpayments_payment_id }}</span>
                    </div>
                    @endif
                @endif
            </div>
        </div>
        
        <!-- Payment Summary -->
        <div class="payment-summary">
            <h3>Total Amount Paid</h3>
            <div class="amount">${{ number_format($data['order']->amount, 2) }}</div>
            <p>Service: Tip Payment</p>
            <p>Description: Payment for order #{{ $data['order']->id }}</p>
        </div>
        
        <!-- Crypto Payment Details (if applicable) -->
        @if($data['order']->isCryptoPayment() && $data['order']->amount_received > 0)
        <div class="crypto-details">
            <h4>Crypto Payment Details</h4>
            <div class="detail-row">
                <span class="detail-label">Amount Received:</span>
                <span class="detail-value">{{ number_format($data['order']->amount_received, 8) }} {{ strtoupper($data['order']->pay_currency ?? $data['order']->crypto_currency) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status:</span>
                <span class="detail-value">{{ ucfirst($data['order']->payment_status ?? 'Completed') }}</span>
            </div>
            @if($data['order']->is_fixed_rate)
            <div class="detail-row">
                <span class="detail-label">Rate Type:</span>
                <span class="detail-value">Fixed Rate</span>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your payment!</strong></p>
            <p>This invoice was generated on {{ $data['generated_at'] }}</p>
            <p>For any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>
