<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{$order->order_number ?? 'Order'}}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 700px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .logo {
            width: 60px;
            height: 60px;
            background-color: #3498db;
            color: white;
            text-align: center;
            line-height: 60px;
            font-size: 24px;
            font-weight: bold;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }
        .invoice-title {
            text-align: center;
            margin: 20px 0;
        }
        .invoice-title h1 {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        .invoice-number {
            background-color: #f8f9fa;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
            border: 1px solid #ddd;
        }
        .billing-section {
            margin-bottom: 20px;
        }
        .billing-section table {
            width: 100%;
            border: 1px solid #ddd;
        }
        .billing-section td {
            padding: 15px;
            vertical-align: top;
        }
        .bill-to {
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .customer-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .customer-detail {
            margin-bottom: 5px;
            color: #555;
        }
        .order-info {
            text-align: right;
        }
        .order-info div {
            margin-bottom: 8px;
        }
        .order-label {
            color: #666;
            font-weight: 500;
        }
        .order-value {
            color: #2c3e50;
            font-weight: bold;
        }
        .unpaid {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
        }
        .paid {
            color: #27ae60;
            background-color: #d5f4e6;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
        }
        .products-table {
            width: 100%;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .products-table th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 11px;
            padding: 10px 8px;
            text-align: left;
        }
        .products-table td {
            border-bottom: 1px solid #ddd;
            padding: 10px 8px;
            vertical-align: top;
        }
        .products-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }
        .product-detail {
            color: #555;
        }
        .summary-table {
            width: 300px;
            border: 1px solid #ddd;
            margin-left: auto;
        }
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #ddd;
        }
        .summary-table tr:last-child td {
            border-bottom: none;
        }
        .summary-label {
            color: #555;
            font-weight: 500;
        }
        .summary-value {
            color: #2c3e50;
            font-weight: bold;
            text-align: right;
        }
        .grand-total {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .grand-total .summary-value {
            color: white;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .thank-you {
            font-size: 16px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 25px;
            text-align: right;
        }
        .signature-line {
            border-top: 2px solid #2c3e50;
            width: 150px;
            margin: 0 auto 5px auto;
        }
        .signature-text {
            font-size: 11px;
            color: #666;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="container">
    @if($order)
        <div class="header">
            <table>
                <tr>
                    <td style="width: 80px;">
                        <div class="logo">⚙</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="company-name">Yantratools</div>
                        <div class="company-info">
                            India's Largest Online machinery market place<br>
                            Near Royal Pearl Complex, Benachity, Durgapur<br>
                            West Bengal 713213<br>
                            Email: Yantratools@gmail.com<br>
                            Phone: India's Largest Online machinery market place
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="invoice-number">#{{ $order->order_number ?? 'N/A' }}</div>
        </div>

        <div class="billing-section">
            <table>
                <tr>
                    <td class="bill-to" style="width: 50%;">
                        <div class="section-title">Bill to:</div>
                        <div class="customer-name">{{ $order->user->name ?? 'Customer Name' }}</div>
                        <div class="customer-detail">{{ $order->address->address ?? '' }} {{ $order->address->address2 ?? '' }}</div>
                        <div class="customer-detail">Email: {{ $order->user->email ?? 'email@example.com' }}</div>
                        <div class="customer-detail">Phone: {{ $order->user->mobile ?? 'Phone Number' }}</div>
                        <div class="customer-detail">GSTIN: NA</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="order-info">
                            <div><span class="order-label">Order ID:</span> <span class="order-value">{{ $order->order_number ?? 'N/A' }}</span></div>
                            <div><span class="order-label">Order Date:</span> <span class="order-value">{{ $order->created_at ? $order->created_at->format('d/m/Y, H:i') : 'N/A' }}</span></div>
                            <div><span class="order-label">Payment Status:</span>
                                <span class="{{ $order->payment_status == 'paid' ? 'paid' : 'unpaid' }}">
                                    {{ $order->payment_status ?? 'unpaid' }}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="products-table">
            <thead>
            <tr>
                <th style="width: 35%;">Product Name</th>
                <th style="width: 15%;">Delivery Type</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 12%;">Tax</th>
                <th style="width: 13%;">Total</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($order->cart_info) && count($order->cart_info) > 0)
                @foreach($order->cart_info as $cart)
                    @php
                        $product = DB::table('products')->select('title')->where('id', $cart->product_id)->first();
                        $tax = $cart->price * 0.18;
                        $total = $cart->price + $tax;
                    @endphp
                    <tr>
                        <td class="product-name">{{ $product->title ?? 'Product' }}</td>
                        <td class="product-detail">home_delivery</td>
                        <td class="text-right product-detail">{{ $cart->quantity }}</td>
                        <td class="text-right product-detail">Rs.{{ number_format($cart->price, 2) }}</td>
                        <td class="text-right product-detail">Rs.{{ number_format($tax, 2) }}</td>
                        <td class="text-right product-name">Rs.{{ number_format($total, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">No products found</td>
                </tr>
            @endif
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="summary-label">Sub Total:</td>
                <td class="summary-value">Rs.{{ number_format($order->sub_total ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Total Tax:</td>
                <td class="summary-value">Rs.{{ number_format(($order->sub_total ?? 0) * 0.18, 2) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Shipping Cost:</td>
                <td class="summary-value">Rs.{{ number_format($order->delivery_charge ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="summary-label">Full payment Discount:</td>
                <td class="summary-value">Rs.{{ number_format(($order->sub_total ?? 0) * 0.03, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="summary-value">Rs.{{ number_format($order->total_amount ?? 0, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="thank-you">Thank you for your business !!</div>
            <div style="color: #666; font-size: 11px;">
                We appreciate your trust in Yantratools. For any queries, please contact our support team.
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-text">Authority Signature</div>
        </div>
    @else
        <div style="text-align: center; padding: 60px; color: #e74c3c;">
            <h2 style="margin: 0;">Invalid Order</h2>
            <p style="margin: 10px 0 0 0; color: #666;">The requested order could not be found.</p>
        </div>
    @endif
</div>
</body>
</html>
