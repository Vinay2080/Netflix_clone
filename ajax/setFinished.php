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

        $query = $con->prepare("UPDATE videoProgress 
                              SET finished = 1, progress = 0, dateModified = NOW()
                              WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);

        if ($query->execute()) {
            echo json_encode(["success" => true]);
        } else {
            throw new Exception("Failed to update video status");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters"]);
}