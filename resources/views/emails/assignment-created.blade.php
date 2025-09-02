<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Baru - {{ $assignment->title }}</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .assignment-card {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
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
        .difficulty {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .difficulty.easy { background: #d4edda; color: #155724; }
        .difficulty.medium { background: #fff3cd; color: #856404; }
        .difficulty.hard { background: #f8d7da; color: #721c24; }
        .deadline {
            background: #e7f3ff;
            border: 1px solid #b8daff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .deadline-icon {
            color: #007bff;
            font-size: 18px;
            margin-right: 10px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #007bff, #0056b3);
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
        }
        .tips {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .tips h4 {
            margin: 0 0 10px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Tugas Baru Tersedia!</h1>
            <p>Halo! Ada assignment baru yang perlu kamu kerjakan</p>
        </div>

        <div class="content">
            <div class="assignment-card">
                <h2 style="margin-top: 0; color: #007bff;">{{ $assignment->title }}</h2>
                <p style="color: #666; margin-bottom: 0;">{{ $assignment->description }}</p>
            </div>

            <div class="info-row">
                <span class="info-label">📂 Kategori:</span>
                <span class="info-value">{{ ucfirst($assignment->category) }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">⚡ Tingkat Kesulitan:</span>
                <span class="info-value">
                    <span class="difficulty {{ $assignment->difficulty }}">
                        {{ ucfirst($assignment->difficulty) }}
                    </span>
                </span>
            </div>

            @if($assignment->target_class)
            <div class="info-row">
                <span class="info-label">🎯 Target Kelas:</span>
                <span class="info-value">{{ $assignment->target_class }}</span>
            </div>
            @endif

            <div class="deadline">
                <span class="deadline-icon">⏰</span>
                <strong>Deadline:</strong>
                {{ $assignment->deadline->format('d F Y, H:i') }} WIB
                <br>
                <small style="color: #666;">
                    ({{ $assignment->deadline->diffForHumans() }})
                </small>
            </div>

            <div style="text-align: center;">
                <a href="{{ $url }}" class="cta-button">
                    🚀 Mulai Kerjakan Sekarang
                </a>
            </div>

            <div class="tips">
                <h4>💡 Tips Pengerjaan:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Baca deskripsi tugas dengan teliti</li>
                    <li>Perhatikan deadline yang telah ditentukan</li>
                    <li>Pastikan repository GitHub kamu public</li>
                    <li>Jangan ragu bertanya jika ada yang kurang jelas</li>
                </ul>
            </div>

            <p style="color: #666; font-style: italic; text-align: center;">
                Semangat mengerjakan! 💪<br>
                Tim IT Club
            </p>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis dari sistem Task Management IT Club</p>
            <p>Jika ada pertanyaan, silakan hubungi admin IT Club</p>
        </div>
    </div>
</body>
</html>
