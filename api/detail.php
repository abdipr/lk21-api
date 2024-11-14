<?php
header("Content-Type: application/json");
require "simple_html_dom.php";
$id = $_GET['id'] ?? '';
if (!$id) {
    echo json_encode([
        "status" => "404",
        "author" => "abdiputranar",
        "message" => "Parameter 'id' diperlukan, contoh: ?id=greatest-showman-2017"
    ], JSON_PRETTY_PRINT);
    exit;
}

$web = "https://tv6.lk21official.my";
$url = $web . "/" . $id;

$options = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"
    ]
];
$context = stream_context_create($options);

$htmlContent = file_get_contents($url, false, $context);
if (!$htmlContent) {
    echo json_encode([
        "status" => "404",
        "author" => "abdiputranar",
        "message" => "Halaman tidak ditemukan"
    ], JSON_PRETTY_PRINT);
    exit;
}

$html = str_get_html($htmlContent);
if (!$html) {
    echo json_encode([
        "status" => "404",
        "author" => "abdiputranar",
        "message" => "Halaman tidak valid"
    ], JSON_PRETTY_PRINT);
    exit;
}

$title = $html->find('div.col-xs-9.content blockquote a', 0)->plaintext;
$image = "https:" . $html->find('picture img', 0)->src;
$kualitas = $html->find('div.col-xs-9.content div', 0)->find('h3', 0)->plaintext;
$negara = $html->find('div.col-xs-9.content div', 1)->find('h3', 0)->plaintext;
$pemeran = [];
foreach ($html->find('div.col-xs-9.content div', 2)->find('h3') as $castElement) {
    $pemeran[] = $castElement->plaintext;
}
$sutradara = $html->find('div.col-xs-9.content div', 3)->find('h3', 0)->plaintext;
$genre = [];
foreach ($html->find('div.col-xs-9.content div', 4)->find('h3 a') as $genreElement) {
    $genre[] = $genreElement->plaintext;
}
$rating = $html->find('div.col-xs-9.content div', 5)->find('h3', 0)->plaintext;
$rilis = $html->find('div.col-xs-9.content div', 6)->find('h3', 0)->plaintext;
$fullText = $html->find('div.col-xs-9.content', 0)->plaintext;
preg_match('/(\d+\s?jam\s\d+\s?menit|\d+\s?jam|\d+\s?menit)/i', $fullText, $matches);

$durasi = $matches[0] ?? 'Durasi tidak ditemukan';
$sinopsisFull = $html->find('div.col-xs-9.content blockquote', 0)->plaintext;
$sinopsisOnly = str_replace("Synopsis\r\n" . $title . " ", '', $sinopsisFull);
preg_match('/^(.*?)(?=\r\nBudget:|$)(?:\r\nBudget: (.*?))?(?:\r\nWorldwide Gross: (.*?))?(?:\r\nSoundtrack: (.*?))?$/s', $sinopsisOnly, $matches);
$sinopsis = trim($matches[1] ?? '');
$sinopsis = preg_replace('/^(.*\.).*$/s', '$1', $sinopsis);
$budget = trim($matches[2] ?? '');
$gross = trim($matches[3] ?? '');
$soundtrack = trim($matches[4] ?? '');
$soundtrack = preg_replace('/^(.*?\)).*$/s', '$1', $soundtrack);

$trailer = $html->find('div.action-player ul li a', 2)->href;
$iframe = $html->find('div#loadPlayer iframe#player-iframe', 0)->src;
$iframe = str_replace("https://playeriframe.lol/iframe.php?url=", "", $iframe);
$iframe = urldecode($iframe);


$filmDetail = [
    "id" => $id,
    "judul" => $title,
    "image" => $image,
    "kualitas" => $kualitas,
    "negara" => $negara,
    "pemeran" => $pemeran,
    "sutradara" => $sutradara,
    "genre" => $genre,
    "rating_imdb" => $rating,
    "rilis" => $rilis,
    "durasi" => $durasi,
    "sinopsis" => $sinopsis,
    "budget" => $budget,
    "worldwide_gross" => $gross,
    "soundtrack" => $soundtrack,
    "trailer" => $trailer,
    "iframe" => $iframe,
];

echo str_replace("\\/", "/", json_encode([
    "status" => "200",
    "author" => "abdiputranar",
    "data" => $filmDetail
], JSON_PRETTY_PRINT));
?>
