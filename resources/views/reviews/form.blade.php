<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Kamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling untuk container utama */
        .review-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        /* Styling untuk judul */
        h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Styling untuk select dropdown */
        .form-control {
            border-radius: 5px;
            padding: 10px;
        }

        /* Styling untuk textarea */
        .review-textarea {
            height: 120px;
            resize: none;
        }

        /* Styling untuk tombol submit */
        .btn-block {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        /* Hover efek pada tombol */
        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Animasi untuk alert sukses */
        .alert-success {
            animation: fadeIn 1s ease-in-out;
            text-align: center;
        }

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
    <div class="review-container">
        <h2>Review Kamar 🏨</h2>

        <!-- Notifikasi sukses -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; ?>
            </div>
        <?php unset($_SESSION['success']); endif; ?>

        <form action="{{ route('review.store', ['token' => $review->token]) }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            
            <div class="mb-3">
                <label class="form-label">Rating:</label>
                <select name="rating" class="form-control" required>
                    <option value="">-- Pilih Rating --</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= isset($review->rating) && $review->rating == $i ? 'selected' : '' ?>>
                            <?= $i ?> ⭐
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Review:</label>
                <textarea name="review" class="form-control review-textarea" placeholder="Tulis pengalaman Anda di sini..."><?= isset($review->review) ? htmlspecialchars($review->review) : '' ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Kirim Review</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
