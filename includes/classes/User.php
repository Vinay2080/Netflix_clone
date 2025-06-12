<?php

class User
{
    private $con, $sqlData;

    public function __construct($con, $username)
    {
        if (!$con) {
            throw new Exception("Database connection cannot be null");
        }

        $this->con = $con;
        $this->sqlData = $this->getUserData($username);
    }

    private function getUserData($username)
    {
        $query = $this->con->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $query->bindValue(":username", $username, PDO::PARAM_STR);

        if (!$query->execute()) {
            throw new Exception("Failed to fetch user data");
        }

        $data = $query->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new Exception("User not found");
        }

        return $data;
    }

    public function getFirstName()
    {
        return htmlspecialchars($this->sqlData["firstName"] ?? '');
    }

    public function getLastName()
    {
        return htmlspecialchars($this->sqlData["lastName"] ?? '');
    }

    public function getEmail()
    {
        return filter_var($this->sqlData["email"] ?? '', FILTER_SANITIZE_EMAIL);
    }

    public function getUsername()
    {
        return htmlspecialchars($this->sqlData["username"] ?? '');
    }

    public function isSubscribed()
    {
        return (bool)($this->sqlData["isSubscribed"] ?? false);
    }
}