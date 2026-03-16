@extends('layouts.admin')

@section('title', 'Reports')
@section('subtitle', 'Generate and download reports for your store')

@section('content')
<div class="reports-container">
    <div class="report-grid">
        @php
            $reports = [
                ['name' => 'Sales Report', 'desc' => 'Detailed sales analysis and revenue breakdown', 'icon' => 'trending-up', 'color' => '#3b82f6', 'format' => 'PDF'],
                ['name' => 'Inventory Report', 'desc' => 'Stock levels and product availability', 'icon' => 'package', 'color' => '#10b981', 'format' => 'Excel'],
                ['name' => 'Customer Report', 'desc' => 'Customer data and purchasing patterns', 'icon' => 'users', 'color' => '#a855f7', 'format' => 'CSV'],
                ['name' => 'Performance Report', 'desc' => 'Key metrics and business insights', 'icon' => 'activity', 'color' => '#f59e0b', 'format' => 'PDF'],
                ['name' => 'Financial Report', 'desc' => 'Payment transactions and revenue', 'icon' => 'dollar-sign', 'color' => '#ec4899', 'format' => 'Excel'],
                ['name' => 'Reviews Report', 'desc' => 'Customer feedback and ratings analysis', 'icon' => 'star', 'color' => '#8b5cf6', 'format' => 'CSV'],
            ];
        @endphp

        @foreach($reports as $report)
        <div class="card" style="padding: 32px; display: flex; flex-direction: column;">
            <div class="report-icon-circle" style="background-color: {{ $report['color'] }}15;">
                <i data-lucide="{{ $report['icon'] }}" style="color: {{ $report['color'] }}; width: 22px;"></i>
            </div>
            <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--text-dark);">{{ $report['name'] }}</h3>
            <p style="font-size: 13px; color: var(--text-medium); margin-bottom: 24px; line-height: 1.5; flex: 1;">
                {{ $report['desc'] }}
            </p>
            <button class="btn btn-outline" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;" onclick="handleDownload(this, '{{ $report['format'] }}')">
                <i data-lucide="download" style="width: 16px;"></i>
                Export {{ $report['format'] }}
            </button>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleDownload(btn, format) {
        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner" style="border: 2px solid rgba(0,0,0,0.1); border-top: 2px solid #3b82f6; border-radius: 50%; width: 14px; height: 14px; display: inline-block; animation: spin 0.8s linear infinite; margin-right: 8px;"></span> Downloading...`;
        
        // Add spinner animation style if not present
        if (!document.getElementById('spinner-style')) {
            const style = document.createElement('style');
            style.id = 'spinner-style';
            style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }

        setTimeout(() => {
            const reportName = btn.closest('.card').querySelector('h3').innerText;
            let blob;
            let extension = format.toLowerCase();
            let filename = `${reportName.replace(/\s+/g, '_')}_Report`;

            if (format === 'Excel') {
                // Use HTML table format for actual Excel column separation
                // We break the <x: tags to prevent Blade from interpreting them as components
                const tableHtml = `
                    <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                    <head><meta charset="UTF-8"><!--[if gte mso 9]><xml><x` + `:ExcelWorkbook><x` + `:ExcelWorksheets><x` + `:ExcelWorksheet><x` + `:Name>${reportName}</x` + `:Name><x` + `:WorksheetOptions><x` + `:DisplayGridlines/></x` + `:WorksheetOptions></x` + `:ExcelWorksheet></x` + `:ExcelWorksheets></x` + `:ExcelWorkbook></xml><![endif]--></head>
                    <body>
                        <h2 style="font-family: sans-serif;">${reportName}</h2>
                        <p style="font-family: sans-serif; color: #666;">Generated on: ${new Date().toLocaleString()}</p>
                        <table border="1" style="border-collapse: collapse; font-family: sans-serif;">
                            <tr style="background-color: #f3f4f6;">
                                <th style="padding: 10px;">Date</th>
                                <th style="padding: 10px;">Order ID</th>
                                <th style="padding: 10px;">Customer</th>
                                <th style="padding: 10px;">Amount</th>
                                <th style="padding: 10px;">Status</th>
                            </tr>
                            <tr><td style="padding: 8px;">2026-03-16</td><td style="padding: 8px;">1001</td><td style="padding: 8px;">Juana Dela Cruz</td><td style="padding: 8px;">₱1,250.00</td><td style="padding: 8px;">Delivered</td></tr>
                            <tr><td style="padding: 8px;">2026-03-16</td><td style="padding: 8px;">1002</td><td style="padding: 8px;">John Doe</td><td style="padding: 8px;">₱850.00</td><td style="padding: 8px;">Processing</td></tr>
                            <tr><td style="padding: 8px;">2026-03-16</td><td style="padding: 8px;">1003</td><td style="padding: 8px;">Maria Santos</td><td style="padding: 8px;">₱3,400.00</td><td style="padding: 8px;">Pending</td></tr>
                        </table>
                    </body>
                    </html>`;
                blob = new Blob([tableHtml], { type: 'application/vnd.ms-excel' });
                extension = 'xls';
            } else if (format === 'CSV') {
                // Add BOM for UTF-8 (important for Excel to show symbols like ₱ correctly)
                const csvHeader = "\uFEFFDate,Order ID,Customer,Amount,Status\n";
                const csvData = "2026-03-16,1001,Juana Dela Cruz,₱1250.00,Delivered\n2026-03-16,1002,John Doe,₱850.00,Processing\n2026-03-16,1003,Maria Santos,₱3400.00,Pending\n";
                blob = new Blob([csvHeader + csvData], { type: 'text/csv;charset=utf-8' });
            } else if (format === 'PDF') {
                // Using a simpler, cleaner text format for the mock PDF
                const docHeader = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R>>endobj 4 0 obj<</Length 500>>stream\n";
                const docTitle = `BT /F1 16 Tf 50 750 Td (${reportName}) Tj ET\n`;
                const docSub = `BT /F1 10 Tf 50 730 Td (Generated on: ${new Date().toLocaleString()}) Tj ET\n`;
                const docTable = `BT /F1 12 Tf 50 700 Td (Date | Order ID | Customer | Amount | Status) Tj 0 -20 Td (2026-03-16 | 1001 | Juana Dela Cruz | P1,250.00 | Delivered) Tj 0 -20 Td (2026-03-16 | 1002 | John Doe | P850.00 | Processing) Tj 0 -20 Td (2026-03-16 | 1003 | Maria Santos | P3,400.00 | Pending) Tj ET\n`;
                const docFooter = "endstream\nendobj\nxref\n0 5\n0000000000 65535 f\n0000000009 00000 n\n0000000052 00000 n\n0000000101 00000 n\n0000000192 00000 n\ntrailer<</Size 5/Root 1 0 R>>\nstartxref\n293\n%%EOF";
                blob = new Blob([docHeader + docTitle + docSub + docTable + docFooter], { type: 'application/pdf' });
            }

            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `${filename}.${extension}`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);

            btn.innerHTML = originalContent;
            btn.disabled = false;
            showToast(`${reportName} exported successfully`);
        }, 1500);
    }
</script>
@endsection
