<?php
header("Content-Type: application/json");
echo str_replace("\\", "", json_encode(["status" => "200","author" => "abdiputranar","message" => "Check https://github.com/abdipr/lk21-api for documentation"], JSON_PRETTY_PRINT));
?>
