<?php
require_once("../includes/config.php");

if (!isset($con)) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

header('Content-Type: application/json');

if (isset($_POST["videoId"], $_POST["username"], $_POST["progress"])) {
    try {
        $videoId = $_POST["videoId"];
        $username = $_POST["username"];
        $progress = floatval($_POST["progress"]);

        // Input validation
        if (empty($videoId) || empty($username)) {
            throw new Exception("Video ID and username cannot be empty");
        }

        $query = $con->prepare("UPDATE videoProgress 
                              SET progress = :progress, dateModified = NOW() 
                              WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);
        $query->bindParam(":progress", $progress, PDO::PARAM_STR);

        if ($query->execute()) {
            echo json_encode(["success" => true]);
        } else {
            throw new Exception("Failed to update progress");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters"]);
}