<?php

/**
 * FILE: Admin/ReportController.php
 * 
 * What this file does:
 * This controller generates business reports for the admin.
 * It provides summaries of sales, top products, and monthly revenue.
 * 
 * How it connects to the project:
 * - It is called by the route "admin.reports" in routes/admin.php.
 * - It uses the ReportService to handle the heavy mathematical calculations.
 * - It returns the resources/views/admin/reports.blade.php view.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;

class ReportController extends Controller
{
    // Holds the ReportService which calculates all the report numbers.
    protected $reportService;

    // The constructor runs automatically and injects the ReportService.
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Shows the reports and analytics page.
     * 
     * This function asks the ReportService to gather all the statistics
     * (like Monthly Sales and Best Selling Products) and sends them to the view.
     * 
     * @return view — the reports page
     */
    public function index()
    {
        // Get all the pre-calculated reports from the helper service.
        $allData = $this->reportService->getAllReports();
        
        // Pass the data to the reports view.
        return view('admin.reports', compact('allData'));
    }
}
