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

        // Input validation
        if (empty($videoId) || empty($username)) {
            throw new Exception("Video ID and username cannot be empty");
        }

        $query = $con->prepare("SELECT id FROM videoProgress 
                              WHERE username = :username AND videoId = :videoId");
        $query->bindParam(":username", $username);
        $query->bindParam(":videoId", $videoId);
        $query->execute();

        if ($query->rowCount() == 0) {
            $insert = $con->prepare("INSERT INTO videoProgress (username, videoId, progress, dateModified)
                                   VALUES(:username, :videoId, 0, NOW())");
            $insert->bindParam(":username", $username);
            $insert->bindParam(":videoId", $videoId);

            if ($insert->execute()) {
                echo json_encode(["success" => true]);
            } else {
                throw new Exception("Failed to insert video progress");
            }
        } else {
            echo json_encode(["success" => true, "message" => "Record already exists"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters"]);
}