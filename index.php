<?php
$apiKey = "93f6ca50";
$movieData = null;
$errorMessage = "";

$isJsonRequest = isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "application/json") !== false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = urlencode($_POST['title']);
    $apiUrl = "http://www.omdbapi.com/?apikey=$apiKey&t=$title";

    $response = @file_get_contents($apiUrl);

    if ($response === FALSE) {
        $errorMessage = "Gagal mengambil data dari API.";
    } else {
        $movieData = json_decode($response, true);

        if ($movieData["Response"] == "False") {
            $errorMessage = "Film tidak ditemukan!";
            $movieData = null;
        }
    }

    if ($isJsonRequest) {
        header("Content-Type: application/json");
        echo json_encode([
            "success" => $movieData !== null,
            "message" => $errorMessage ?: "Data ditemukan",
            "data" => $movieData
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian Film</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4b6cb7, #182848);
            text-align: center;
            padding-bottom: 50px;
        }
        .navbar {
            background-color: #007BFF;
            padding: 15px;
            color: white;
            font-size: 22px;
            font-weight: bold;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 20px;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        input[type="text"] {
            width: 75%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #0056b3; }
        .movie-info {
            text-align: left;
            margin-top: 20px;
        }
        img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
        }
        h2 { margin: 10px 0; }
        .error { color: red; font-weight: bold; }
        .footer {
            background-color: #007BFF;
            color: white;
            padding: 15px;
            margin-top: 30px;
            font-size: 14px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        @media screen and (max-width: 768px) {
            .container { width: 90%; padding: 15px; }
            input[type="text"] { width: 70%; }
        }
    </style>
</head>
<body>

    <div class="navbar">Pencarian Film</div>

    <div class="container">
        <h2>Cari Film Favoritmu</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Masukkan judul film" required>
            <button type="submit">Cari</button>
        </form>

        <?php if (!empty($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if ($movieData): ?>
            <h2><?php echo htmlspecialchars($movieData["Title"]); ?> (<?php echo htmlspecialchars($movieData["Year"]); ?>)</h2>
            <img src="<?php echo htmlspecialchars($movieData["Poster"]); ?>" alt="Poster Film">
            <div class="movie-info">
                <p><b>Genre:</b> <?php echo htmlspecialchars($movieData["Genre"]); ?></p>
                <p><b>Director:</b> <?php echo htmlspecialchars($movieData["Director"]); ?></p>
                <p><b>Plot:</b> <?php echo htmlspecialchars($movieData["Plot"]); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="footer">
        2025 Pencarian Film. Semua Hak Dilindungi
    </div>

</body>
</html>