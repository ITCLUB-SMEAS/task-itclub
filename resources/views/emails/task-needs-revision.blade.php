<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Perlu Revisi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .revision-card {
            background: #fff5f5;
            border-left: 4px solid #e53e3e;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #856404;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .feedback-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .feedback-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 8px;
        }
        .feedback-content {
            color: #343a40;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Tugas Perlu Revisi</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $task->user->name }}</strong>,</p>

            <p>Tugas Anda perlu direvisi. Admin telah memberikan catatan revisi untuk tugas Anda.</p>

            <div class="revision-card">
                <h2>Informasi Tugas</h2>

                @if($task->assignment)
                <div class="info-row">
                    <span class="info-label">Judul Assignment:</span>
                    <span class="info-value">{{ $task->assignment->title }}</span>
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Link GitHub:</span>
                    <span class="info-value">{{ $task->github_link }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Tanggal Mengumpulkan:</span>
                    <span class="info-value">{{ $task->tanggal_mengumpulkan->format('d/m/Y') }}</span>
                </div>

                @if($task->assignment)
                <div class="info-row">
                    <span class="info-label">Deadline:</span>
                    <span class="info-value">{{ $task->assignment->deadline->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>

            <div class="feedback-box">
                <div class="feedback-title">Catatan dari Admin:</div>
                <div class="feedback-content">
                    "{{ $task->catatan_admin ?: 'Tidak ada catatan spesifik. Silahkan periksa kembali tugas Anda dan pastikan semua persyaratan terpenuhi.' }}"
                </div>
            </div>

            <div class="warning-box">
                <strong>Penting:</strong> Segera lakukan revisi pada tugas Anda sesuai dengan catatan admin.
                @if($task->assignment && !$task->assignment->isOverdue())
                    Anda masih memiliki waktu hingga {{ $task->assignment->deadline->format('d/m/Y H:i') }} untuk melakukan revisi.
                @endif
            </div>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="cta-button">Lihat Detail & Revisi Tugas</a>
            </div>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon untuk tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} IT Club SMK Elektronika Semarang</p>
        </div>
    </div>
</body>
</html>
