<!-- Clean A4 Professional Print-Ready Invoice Template -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= htmlspecialchars($order['order_number']) ?></title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* PDF Layout Optimization (Zero spacing, A4 Portrait fit) */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Inter', -apple-system, sans-serif; 
            color: #1e293b; 
            background: #f1f5f9; 
            padding: 30px 15px; 
            font-size: 13px; 
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Corner Paid Banner (Patta) inspired by HostPerty PDF */
        .corner-ribbon-wrapper {
            position: absolute;
            top: 0;
            right: 0;
            width: 140px;
            height: 140px;
            overflow: hidden;
            z-index: 10;
            pointer-events: none;
        }
        .corner-ribbon {
            background: #22c55e;
            color: #ffffff;
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            letter-spacing: 1.5px;
            padding: 8px 0;
            width: 200px;
            position: absolute;
            top: 25px;
            right: -50px;
            transform: rotate(45deg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-transform: uppercase;
        }

        .invoice-wrapper {
            max-width: 820px;
            margin: 0 auto;
            position: relative;
        }
        
        .invoice-box { 
            position: relative;
            background: #ffffff; 
            border: 1px solid #e2e8f0;
            border-radius: 8px; 
            padding: 40px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01), 0 2px 4px -1px rgba(0,0,0,0.01); 
            z-index: 2;
        }

        /* Header Rows */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .header-left {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-img {
            height: 36px;
            max-width: 160px;
            object-fit: contain;
        }

        .logo-fallback-text {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .company-address-block {
            font-size: 12.5px;
            color: #475569;
            margin-top: 5px;
            line-height: 1.4;
        }

        .header-right {
            text-align: right;
            padding-right: 145px; /* Fully clears the PAID corner ribbon */
        }

        .header-right h2 {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 5px;
        }

        /* Gray Metadata Stripe (Invoice #, Date, Due Date inline) */
        .metadata-stripe {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 12.5px;
        }

        .metadata-stripe div {
            color: #475569;
        }

        .metadata-stripe strong {
            color: #0f172a;
        }

        /* Invoiced To Block (Minimal and compact) */
        .invoiced-to-section {
            margin-bottom: 25px;
            font-size: 12.5px;
        }

        .invoiced-to-section h4 {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .invoiced-to-section p {
            margin-bottom: 2px;
            color: #334155;
        }

        /* Single Line stacked customer details */
        .customer-meta-line {
            font-weight: 500;
        }

        /* Items Table */
        .table-container {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        table.items-table th {
            background: #f8fafc;
            text-align: left;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
        }

        table.items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        table.items-table tr:last-child td {
            border-bottom: none;
        }

        /* Totals Alignments */
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .summary-box {
            width: 320px;
            font-size: 12.5px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-row:last-of-type {
            border-bottom: none;
        }

        .summary-row.total-row {
            font-weight: 700;
            color: #0f172a;
            border-top: 1.5px solid #cbd5e1;
            padding-top: 8px;
            margin-top: 4px;
        }

        /* Transaction History Block */
        .transactions-block {
            margin-bottom: 30px;
        }

        .transactions-block h4 {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .transactions-table-wrap {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        table.transactions-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table.transactions-table th {
            background: #f8fafc;
            text-align: left;
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
        }

        table.transactions-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        table.transactions-table tr:last-child td {
            border-bottom: none;
        }

        /* Footer policy disclaimer details */
        .footer-wrapper {
            text-align: center;
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 11px;
            color: #94a3b8;
        }

        .footer-wrapper p {
            margin-bottom: 4px;
        }

        /* Action Panel */
        .action-panel {
            display: flex;
            justify-content: space-between;
            max-width: 820px;
            margin: 0 auto 20px auto;
        }

        .btn-action {
            padding: 10px 20px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(37,99,235,0.1);
            text-decoration: none;
        }

        .btn-action:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #ffffff;
            color: #475569;
            border: 1px solid #e2e8f0;
            box-shadow: none;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .invoice-wrapper {
                max-width: 100%;
                width: 100%;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body>

    <!-- Actions Control Panel (No Print) -->
    <div class="action-panel no-print">
        <a href="<?= url('user/orders') ?>" class="btn-action btn-back">
            Back to Dashboard
        </a>
        <button onclick="window.print()" class="btn-action">
            Print / Save PDF
        </button>
    </div>

    <?php
    $siteLogo = $settingModel->get('site_logo', '');
    $siteName = $settingModel->get('site_name', 'DigiWS Private Limited');
    $siteEmail = $settingModel->get('site_email', 'info@digiws.com');
    $siteAddress = $settingModel->get('site_address', 'India');
    $siteUrl = url();
    $siteUrlDomain = parse_url($siteUrl, PHP_URL_HOST) ?: 'www.digiws.com';
    $isPaid = in_array($order['payment_status'], ['paid', 'completed']);
    
    // Fetch transaction list dynamically
    $db = Database::getInstance();
    $transactions = $db->fetchAll("SELECT * FROM payments WHERE order_id = ? ORDER BY id ASC", [$order['id']]);
    if (empty($transactions)) {
        $transactions = [[
            'created_at' => $order['created_at'],
            'gateway' => $order['payment_method'] ?? 'UPI Direct',
            'transaction_id' => $payment['transaction_id'] ?? 'N/A',
            'amount' => $order['total'],
            'status' => 'success'
        ]];
    }

    $gstPercent = (int)$settingModel->get('gst_percent', 18);
    ?>

    <div class="invoice-wrapper">
        <div class="invoice-box">
            
            <!-- Green Paid Ribbon (Patta) on Corner -->
            <?php if ($isPaid): ?>
                <div class="corner-ribbon-wrapper">
                    <div class="corner-ribbon">PAID</div>
                </div>
            <?php endif; ?>

            <!-- Top Header (Logo Left, Address Right) -->
            <div class="header-section">
                <div class="header-left">
                    <div class="logo-wrapper" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                        <?php if (!empty($siteLogo)): ?>
                            <img class="logo-img" src="<?= uploadUrl($siteLogo) ?>" alt="DigiWS Logo">
                        <?php endif; ?>
                        <span class="logo-fallback-text" style="font-size: 18px; font-weight: 800; color: #0f172a; margin-top: 4px;">DigiWS Private Limited</span>
                    </div>
                    <div class="company-address-block" style="margin-top: 4px;">
                        India &bull; info@digiws.com
                    </div>
                </div>
                
                <div class="header-right">
                    <h2>Invoice #<?= htmlspecialchars($order['order_number']) ?></h2>
                </div>
            </div>

            <!-- Metadata Stripe Block -->
            <div class="metadata-stripe">
                <div>Invoice Date: <strong><?= date('l, F jS, Y', strtotime($order['created_at'])) ?></strong></div>
                <div>Due Date: <strong><?= date('l, F jS, Y', strtotime($order['created_at'])) ?></strong></div>
            </div>

            <!-- Invoiced To (User Details in One Line) -->
            <div class="invoiced-to-section" style="margin-bottom: 20px; font-size: 12.5px;">
                <span style="font-weight: 700; color: #64748b; text-transform: uppercase; font-size: 11px; margin-right: 8px;">Invoiced To:</span>
                <span style="font-weight: 600; color: #0f172a;"><?= htmlspecialchars($order['user_name']) ?></span> &bull; 
                <span><?= htmlspecialchars($order['user_email']) ?></span>
                <?php if (!empty($order['user_phone'])): ?>
                    &bull; <span><?= htmlspecialchars($order['user_phone']) ?></span>
                <?php endif; ?>
                &bull; <span>India</span>
            </div>

            <!-- Items Table -->
            <div class="table-container">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align:right; width: 140px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($item['product_title']) ?></strong>
                                <?php if ($item['setup_included'] || $item['support_months']): ?>
                                    <div style="font-size:11.5px; color:#64748b; margin-top:2px;">
                                        <?php if ($item['setup_included']) echo "Includes Setup Support Service &nbsp;"; ?>
                                        <?php if ($item['support_months']) echo "{$item['support_months']} Months Extension Support"; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right; font-weight: 600;"><?= formatPrice($item['subtotal']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals Align Block -->
            <div class="summary-wrapper">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Sub Total</span>
                        <span style="font-weight: 600; color:#0f172a;"><?= formatPrice($order['subtotal']) ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?= $gstPercent ?>.00% IGST</span>
                        <span style="font-weight: 600; color:#0f172a;"><?= formatPrice($order['tax_amount']) ?></span>
                    </div>
                    <?php if ($order['discount'] > 0): ?>
                    <div class="summary-row" style="color:#16a34a;">
                        <span>Discount</span>
                        <span>-<?= formatPrice($order['discount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-row">
                        <span>Credit</span>
                        <span style="font-weight: 600; color:#0f172a;">₹0.00 INR</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span style="font-size: 15px; color:#0f172a;"><?= formatPrice($order['total']) ?></span>
                    </div>
                </div>
            </div>

            <!-- Transaction History Block -->
            <div class="transactions-block">
                <h4>Transactions</h4>
                <div class="transactions-table-wrap">
                    <table class="transactions-table">
                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Gateway</th>
                                <th>Transaction ID</th>
                                <th style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= date('l, F jS, Y', strtotime($txn['created_at'])) ?></td>
                                <td><?= strtoupper($txn['gateway'] ?? 'UPI') ?></td>
                                <td style="font-family:monospace;"><?= htmlspecialchars($txn['transaction_id'] ?? 'N/A') ?></td>
                                <td style="text-align:right; font-weight: 600;"><?= formatPrice($txn['amount']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr style="background:#f8fafc; font-weight:700; border-top: 1.5px solid #cbd5e1;">
                                <td colspan="3" style="color:#0f172a;">Balance</td>
                                <td style="text-align:right; color:#0f172a;">₹0.00 INR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Details -->
            <div class="footer-wrapper">
                <p>PDF Generated on <?= date('l, F jS, Y', strtotime($order['created_at'])) ?></p>
                <p>Thank You for Your Purchase ❤️</p>
                <p>&copy; <?= date('Y') ?> DigiWS Private Limited. All rights reserved.</p>
                <p style="margin-top: 8px; font-weight: 500; font-size: 10.5px;">
                    <a href="<?= url('terms-and-conditions') ?>" target="_blank" style="color: #94a3b8; text-decoration: none; margin: 0 4px;">Terms &amp; Conditions</a> | 
                    <a href="<?= url('refund-policy') ?>" target="_blank" style="color: #94a3b8; text-decoration: none; margin: 0 4px;">Refund Policy</a> | 
                    <a href="<?= url('page/privacy-policy') ?>" target="_blank" style="color: #94a3b8; text-decoration: none; margin: 0 4px;">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>
