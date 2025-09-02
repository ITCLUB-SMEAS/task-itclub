<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .alert {
            background-color: #FEF2F2;
            border-left: 4px solid #EF4444;
            padding: 15px;
            margin-bottom: 20px;
            color: #991B1B;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4F46E5;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6B7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pengingat Deadline Tugas</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>

            <div class="alert">
                <strong>Deadline Mendekat!</strong> Tugas Anda akan berakhir dalam {{ $daysLeft }} hari lagi.
            </div>

            <p>Detail Tugas:</p>
            <ul>
                <li><strong>Judul:</strong> {{ $task->judul_tugas }}</li>
                <li><strong>Deskripsi:</strong> {{ $task->deskripsi_tugas }}</li>
                <li><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($task->deadline)->format('d F Y, H:i') }}</li>
            </ul>

            <p>Pastikan untuk menyelesaikan dan mengumpulkan tugas Anda sebelum batas waktu.</p>

            <a href="{{ url('/student/assignments/' . $task->id) }}" class="button">Lihat Detail Tugas</a>

            <p>Jika Anda membutuhkan bantuan, jangan ragu untuk menghubungi admin.</p>

            <p>Terima kasih,<br>
            Tim IT Club</p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon jangan balas email ini.</p>
        </div>
    </div>
</body>
</html>
