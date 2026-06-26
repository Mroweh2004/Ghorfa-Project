<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Request Details #{{ $transaction->id }}</title>
    <style>
        @page { margin: 14mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.45;
            margin: 0;
        }
        .request-details-title {
            margin: 0 0 4px 0;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .request-details-subtitle {
            margin: 0 0 16px 0;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }
        .request-detail-section {
            margin-bottom: 12px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            page-break-inside: avoid;
        }
        .request-detail-section-title {
            margin: 0 0 10px 0;
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
        }
        .request-details-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .request-details-grid td {
            width: 50%;
            vertical-align: top;
            padding: 4px 8px 8px 0;
        }
        .request-detail-item label {
            display: block;
            font-weight: 600;
            color: #64748b;
            font-size: 10px;
            margin-bottom: 2px;
        }
        .request-detail-item span {
            display: block;
            color: #1e293b;
            font-size: 11px;
        }
        .request-detail-item--full {
            padding-top: 6px;
        }
        .request-detail-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 10px;
        }
        .request-detail-status--pending { background: #fef3c7; color: #b45309; }
        .request-detail-status--confirmed { background: #e0f2fe; color: #0369a1; }
        .request-detail-status--paid,
        .request-detail-status--completed { background: #d1fae5; color: #15803d; }
        .request-detail-status--cancelled_by_buyer,
        .request-detail-status--cancelled_by_seller { background: #fee2e2; color: #991b1b; }
        .request-detail-status--refunded { background: #f3f4f6; color: #6b7280; }
        .request-detail-rules-accepted { font-weight: 600; }
        .request-detail-rules-accepted--yes { color: #166534; }
        .request-detail-rules-accepted--no { color: #991b1b; }
        .request-detail-label--warning { color: #92400e; }
        .request-detail-label--notes { color: #4338ca; }
        .request-detail-box {
            padding: 8px 10px;
            border-radius: 6px;
            font-size: 11px;
            margin-top: 4px;
        }
        .request-detail-box--warning {
            background: #fffbeb;
            border-left: 3px solid #d97706;
            color: #78350f;
        }
        .request-detail-box--notes {
            background: #eef2ff;
            border-left: 3px solid #6366f1;
            color: #3730a3;
        }
        .request-detail-list {
            margin: 4px 0 0 0;
            padding-left: 16px;
            font-size: 11px;
        }
        .request-detail-list li { margin-bottom: 2px; }
        .request-detail-empty { color: #64748b; font-style: italic; font-size: 11px; }
        .rules-amenities-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .rules-amenities-table td { width: 50%; vertical-align: top; padding-right: 10px; }
        .rules-amenities-table label {
            display: block;
            font-weight: 600;
            font-size: 11px;
            color: #475569;
            margin-bottom: 4px;
        }
        .amenity-tag {
            display: inline-block;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            margin: 0 4px 4px 0;
            font-size: 10px;
        }
        .arabic-text {
            direction: rtl;
            unicode-bidi: embed;
            text-align: right;
            font-family: xbriyaz, dejavusans, sans-serif;
        }
    </style>
</head>
<body>
    <h1 class="request-details-title">Request Details</h1>
    <p class="request-details-subtitle">Transaction #{{ $transaction->id }}</p>

    @include('landlord.partials.request-details-body', ['transaction' => $transaction, 'forPdf' => true])
</body>
</html>
