<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

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

        try {
            return Excel::download(new TasksExport($filters), 'daftar-tugas-'.date('Y-m-d').'.xlsx');
        } catch (\Throwable $e) {
            // Fallback to CSV if Excel facade/package is unavailable on the server
            Log::warning('Excel export failed, falling back to CSV', ['error' => $e->getMessage()]);
            return $this->downloadTasksCsv($filters, 'daftar-tugas-'.date('Y-m-d').'.csv');
        }
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

        try {
            return Excel::download(new TasksExport($filters), 'laporan-assignment-'.date('Y-m-d').'.xlsx');
        } catch (\Throwable $e) {
            Log::warning('Excel export (assignment report) failed, falling back to CSV', ['error' => $e->getMessage()]);
            return $this->downloadTasksCsv($filters, 'laporan-assignment-'.date('Y-m-d').'.csv');
        }
    }

    /**
     * Fallback CSV stream generator for tasks export
     */
    protected function downloadTasksCsv(array $filters, string $filename)
    {
        $query = Task::with(['assignment']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['kelas'])) {
            $query->where('kelas', $filters['kelas']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['assignment_id'])) {
            $query->where('assignment_id', $filters['assignment_id']);
        }

        $headings = [
            'ID', 'Nama Siswa', 'Kelas', 'Email', 'Link GitHub', 'Judul Assignment',
            'Tanggal Mengumpulkan', 'Terlambat', 'Status', 'Nilai', 'Catatan Admin',
        ];

        return response()->streamDownload(function () use ($query, $headings) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM to help Excel read UTF-8 properly
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, $headings);

            foreach ($query->latest()->cursor() as $task) {
                fputcsv($out, [
                    $task->id,
                    $task->nama_lengkap,
                    $task->kelas,
                    $task->email,
                    $task->github_link,
                    optional($task->assignment)->title ?? 'Tidak ada',
                    optional($task->tanggal_mengumpulkan)->format('d/m/Y H:i'),
                    $task->is_late ? 'Ya' : 'Tidak',
                    ucfirst($task->status),
                    $task->nilai ?? 'Belum dinilai',
                    $task->catatan_admin ?? '-',
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
