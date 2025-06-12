<?php
require_once("../includes/config.php");
require_once("../includes/classes/SearchResultsProvider.php");
require_once("../includes/classes/EntityProvider.php");
require_once("../includes/classes/Entity.php");
require_once("../includes/classes/PreviewProvider.php");

header('Content-Type: application/json');

if (!isset($con)) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

if (isset($_POST["term"]) && isset($_POST["username"])) {
    try {
        $term = trim($_POST["term"]);
        $username = $_POST["username"];

        if (empty($term) || empty($username)) {
            throw new Exception("Search term and username cannot be empty");
        }

        $searchResultsProvider = new SearchResultsProvider($con, $username);
        $results = $searchResultsProvider->getResults($term);

        echo json_encode([
            "success" => true,
            "html" => $results
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Missing required parameters"]);
}