<?php
header("Content-Type: application/json");
require "simple_html_dom.php";

$query = $_GET['q'] ?? '';
if (!$query) {
    echo json_encode(["status" => "404", "author" => "abdiputranar", "message" => "Parameter 'q' diperlukan, contoh: ?q=The Greatest Showman"], JSON_PRETTY_PRINT);
    exit;
}

$web = "https://tv6.lk21official.my";
$url = $web . "/search.php?s=" . urlencode($query);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
    "Accept-Language: en-US,en;q=0.9",
    "Referer: $web",
]);

$htmlContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$htmlContent) {
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

$films = [];
foreach ($html->find("div.search-item") as $item) {
    $titleElement = $item->find("h3 a", 0);
    $imageElement = $item->find("figure a img", 1);
    $director = $item->find("p", 0)->plaintext;
    $stars = $item->find("p", 1)->plaintext;
    $films[] = [
        "id" => str_replace("/", "", $titleElement->href),
        "judul" => $titleElement->plaintext,
        "image" => $web . ($imageElement->src ?? "/wp-content/themes/dunia21/images/default-lk21.jpg"),
        "sutradara" => trim(str_replace("Sutradara:", "", $director)),
        "pemeran" => trim(str_replace("Bintang:", "", $stars)),
        "link" => $web . $titleElement->href
    ];
}

echo str_replace("\\", "", json_encode([
    "status" => "200",
    "author" => "abdiputranar",
    "data" => $films
], JSON_PRETTY_PRINT));
?>
