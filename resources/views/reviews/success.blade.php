<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling untuk container utama */
        .thank-you-container {
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        /* Styling untuk judul */
        h3 {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }

        /* Styling untuk teks */
        p {
            font-size: 16px;
            color: #555;
            margin-top: 10px;
        }

        /* Styling untuk tombol */
        .btn-primary {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Animasi masuk */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container">
    <div class="thank-you-container">
        <h3>🎉 Terima Kasih atas Review Anda!</h3>
        <p>Review Anda telah berhasil disimpan dan sangat berarti bagi kami.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Kembali ke Beranda</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
