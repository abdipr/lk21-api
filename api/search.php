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
$options = [
    "http" => [
        "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36\r\n"
    ]
];
$html = file_get_html($url, false, stream_context_create($options));

if (!$html) {
    echo json_encode(["status" => "404", "author" => "abdiputranar", "message" => "Halaman tidak ditemukan"], JSON_PRETTY_PRINT);
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
