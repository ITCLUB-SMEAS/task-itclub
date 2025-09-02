<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Task::with(['user', 'assignment']);

        // Apply filters
        if (isset($this->filters['status']) && !empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['kelas']) && !empty($this->filters['kelas'])) {
            $query->where('kelas', $this->filters['kelas']);
        }

        if (isset($this->filters['search']) && !empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->get();
    }

    /**
     * @var Task $task
     */
    public function map($task): array
    {
        return [
            $task->id,
            $task->nama_lengkap,
            $task->kelas,
            $task->email,
            $task->github_link,
            $task->assignment->title ?? 'Tidak ada',
            $task->tanggal_mengumpulkan->format('d/m/Y H:i'),
            $task->is_late ? 'Ya' : 'Tidak',
            ucfirst($task->status),
            $task->nilai ?? 'Belum dinilai',
            $task->catatan_admin ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Siswa',
            'Kelas',
            'Email',
            'Link GitHub',
            'Judul Assignment',
            'Tanggal Mengumpulkan',
            'Terlambat',
            'Status',
            'Nilai',
            'Catatan Admin',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
