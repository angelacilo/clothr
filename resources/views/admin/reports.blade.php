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
    const { jsPDF } = window.jspdf;
    
    // Use the real data passed from the controller
    const systemData = @json($allData);

    function handleDownload(btn, format) {
        const reportName = btn.closest('.card').querySelector('h3').innerText;
        const data = systemData[reportName];
        
        if (!data || !data.rows || data.rows.length === 0) {
            return showToast('No data available for this report');
        }

        const originalContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner" style="border: 2px solid rgba(0,0,0,0.1); border-top: 2px solid white; border-radius: 50%; width: 14px; height: 14px; display: inline-block; animation: spin 0.8s linear infinite; margin-right: 8px;"></span> Downloading...`;
        
        if (!document.getElementById('spinner-style')) {
            const style = document.createElement('style');
            style.id = 'spinner-style';
            style.innerHTML = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }

        setTimeout(() => {
            const fileName = `${reportName.toLowerCase().replace(/\s+/g, '_')}_${new Date().getFullYear()}`;

            if (format === 'PDF') {
                const doc = new jsPDF();
                // Header
                doc.setFontSize(24);
                doc.setTextColor(31, 41, 55); 
                doc.text('CLOTHR', 14, 22);
                
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text(reportName, 14, 32);
                
                doc.setFontSize(9);
                doc.setFont(undefined, 'normal');
                doc.setTextColor(107, 114, 128);
                doc.text(`System Generated Report | ${new Date().toLocaleString()}`, 14, 40);
                
                // Draw a horizontal line under the header
                doc.setDrawColor(229, 231, 235);
                doc.line(14, 45, 196, 45);
                
                // Map rows for PDF
                const tableRows = data.rows.map(row => Object.values(row));
                
                doc.autoTable({
                    startY: 52,
                    head: [data.headers],
                    body: tableRows,
                    theme: 'striped',
                    headStyles: { fillColor: [31, 41, 55], fontSize: 10, cellPadding: 4 },
                    bodyStyles: { fontSize: 9, cellPadding: 3 },
                    alternateRowStyles: { fillColor: [249, 250, 251] },
                    margin: { top: 52 }
                });
                doc.save(`${fileName}.pdf`);
            } 
            else if (format === 'Excel') {
                const tableRows = data.rows.map(row => Object.values(row));
                const aoa = [
                    [reportName],
                    [`Generated on: ${new Date().toLocaleString()}`],
                    [],
                    data.headers,
                    ...tableRows
                ];
                
                const worksheet = XLSX.utils.aoa_to_sheet(aoa);
                
                // Advanced column width calculation for professional look
                const colWidths = data.headers.map((h, i) => {
                    let maxLen = h.length;
                    
                    // Check header length
                    tableRows.forEach(row => {
                        const val = row[i] ? row[i].toString() : '';
                        if (val.length > maxLen) maxLen = val.length;
                    });
                    
                    // Specific padding for important columns
                    if (h.toLowerCase().includes('email')) return { wch: Math.max(maxLen + 5, 30) };
                    if (h.toLowerCase().includes('name') || h.toLowerCase().includes('product')) return { wch: Math.max(maxLen + 5, 25) };
                    if (h.toLowerCase().includes('date')) return { wch: 18 }; // Prevents #########
                    
                    return { wch: maxLen + 6 };
                });
                
                worksheet['!cols'] = colWidths;

                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Data");
                XLSX.writeFile(workbook, `${fileName}.xlsx`);
            } 
            else if (format === 'CSV') {
                const tableRows = data.rows.map(row => Object.values(row));
                const csvRows = [data.headers, ...tableRows];
                const csvContent = "\uFEFF" + csvRows.map(row => row.map(cell => `"${cell}"`).join(",")).join("\n");
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.style.display = 'none';
                link.href = url;
                link.download = `${fileName}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            btn.innerHTML = originalContent;
            btn.disabled = false;
            showToast(`${reportName} exported successfully`);
        }, 1500);
    }
</script>
@endsection
