<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export tasks to Excel
     */
    public function exportTasks(Request $request)
    {
        // Check admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk ekspor data.');
        }

        $filters = [
            'status' => $request->status,
            'kelas' => $request->kelas,
            'search' => $request->search,
        ];

        return Excel::download(new TasksExport($filters), 'daftar-tugas-'.date('Y-m-d').'.xlsx');
    }

    /**
     * Export assignment report
     */
    public function exportAssignmentReport(Request $request)
    {
        // Check admin role
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk ekspor data.');
        }

        $assignmentId = $request->assignment_id;

        if (!$assignmentId) {
            return redirect()->back()->with('error', 'Assignment ID tidak valid.');
        }

        $filters = [
            'assignment_id' => $assignmentId,
        ];

        return Excel::download(new TasksExport($filters), 'laporan-assignment-'.date('Y-m-d').'.xlsx');
    }
}
