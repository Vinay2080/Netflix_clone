<?php
require_once("../includes/config.php");

if (!isset($con)) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

header('Content-Type: application/json');

if (isset($_POST["videoId"]) && isset($_POST["username"])) {
    try {
        $videoId = $_POST["videoId"];
        $username = $_POST["username"];

        $query = $con->prepare("SELECT progress FROM videoProgress
                              WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        $progress = $query->fetchColumn();
        echo json_encode([
            "success" => true,
            "progress" => $progress !== false ? floatval($progress) : 0
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters"]);
}