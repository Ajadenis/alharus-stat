<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - MDT Al-Harus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 500px;
            border-radius: 1.5rem;
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
            border: none;
            padding: 2.5rem;
            text-align: center;
        }
        .icon-success {
            font-size: 5rem;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-success">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <h2 class="mt-3">✅ Pembayaran Berhasil!</h2>
        <p class="text-muted">Bukti pembayaran telah diupload. Menunggu konfirmasi admin.</p>
        <a href="{{ route('infak.public') }}" class="btn btn-success btn-lg mt-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</body>
</html>