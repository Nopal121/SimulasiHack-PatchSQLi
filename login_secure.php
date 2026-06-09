<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli("localhost", "root", "", "keamanan_db", 3307);

$pesan = "";
$pesan_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['username'] ?? '';
    $pass_input = $_POST['password'] ?? '';

    // KODE AMAN - Prepared Statements
    $sql_aman = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql_aman);
    $stmt->bind_param("ss", $user_input, $pass_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pesan = "Berhasil Login! Selamat datang, <strong>" . htmlspecialchars($row['username']) . "</strong> &mdash; Role: <strong>" . htmlspecialchars($row['role']) . "</strong>";
        $pesan_type = "success";
    } else {
        $pesan = "Gagal Login! Sistem memblokir injeksi SQL.";
        $pesan_type = "error";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQLi Lab — Versi Aman</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 1rem;
        }

        .wrapper { width: 100%; max-width: 420px; }

        .badge-lab {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 99px;
            width: fit-content;
            margin: 0 auto 16px;
        }

        .badge-lab::before {
            content: '';
            width: 8px; height: 8px;
            background: #22c55e;
            border-radius: 50%;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            background: #166534;
            padding: 24px 28px;
            text-align: center;
        }

        .card-header h1 { color: #fff; font-size: 20px; font-weight: 700; }
        .card-header p { color: #bbf7d0; font-size: 13px; margin-top: 4px; }

        .card-body { padding: 28px; }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
        }

        .alert-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
        .alert-error   { background: #fff1f1; border: 1px solid #fca5a5; color: #b91c1c; }
        .alert-icon    { font-size: 16px; flex-shrink: 0; }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            background: #f9fafb;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #22c55e;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #166534;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            margin-top: 4px;
        }

        .btn:hover  { background: #14532d; }
        .btn:active { transform: scale(0.98); }

        .query-box {
            margin-top: 16px;
            padding: 12px 14px;
            background: #1e1e1e;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #d4d4d4;
            word-break: break-all;
            line-height: 1.6;
        }

        .secure-note {
            margin-top: 16px;
            padding: 12px 14px;
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 10px;
            font-size: 12px;
            color: #166534;
            line-height: 1.6;
        }

        .secure-note strong { display: block; margin-bottom: 4px; color: #14532d; }

        code {
            background: #dcfce7;
            border: 1px solid #86efac;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #14532d;
        }

        .footer { text-align: center; margin-top: 16px; font-size: 12px; color: #9ca3af; }

        @media (max-width: 480px) {
            .card-body { padding: 20px; }
            .card-header { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="badge-lab">Secure Version</div>

    <div class="card">
        <div class="card-header">
            <h1>Sistem Login Perusahaan</h1>
            <p>Praktikum Keamanan Web &mdash; Prepared Statements</p>
        </div>

        <div class="card-body">

            <?php if ($pesan): ?>
            <div class="alert alert-<?= $pesan_type ?>">
                <span class="alert-icon"><?= $pesan_type === 'success' ? '✅' : '🛡️' ?></span>
                <span><?= $pesan ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($user_input)): ?>
            <div class="query-box">
                <span style="color:#9ca3af;font-size:11px;">Query Prepared Statement:</span><br><br>
                <span style="color:#86efac;">SELECT * FROM users WHERE username = ? AND password = ?</span><br><br>
                <span style="color:#9ca3af;font-size:11px;">Parameter (diperlakukan sebagai teks literal):</span><br>
                [1] = "<?= htmlspecialchars($user_input) ?>"<br>
                [2] = "<?= htmlspecialchars($pass_input) ?>"
            </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off" style="margin-top: <?= !empty($user_input) ? '16px' : '0' ?>">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           placeholder="Coba masukkan payload..."
                           value="<?= htmlspecialchars($user_input ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Masukkan password...">
                </div>

                <button type="submit" class="btn">Masuk</button>
            </form>

            <div class="secure-note">
                <strong>🛡️ Catatan Lab &mdash; Versi Aman</strong>
                Payload <code>' OR '1'='1'#</code> akan <strong>ditolak</strong> karena
                Prepared Statements memisahkan struktur query dan data.
                MySQL memperlakukan input sebagai teks biasa, bukan perintah SQL.
            </div>
        </div>
    </div>

    <p class="footer">SQLi Lab &mdash; Praktikum Keamanan Web</p>
</div>
</body>
</html>