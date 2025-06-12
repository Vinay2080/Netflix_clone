<?php

class entity
{
    private $con, $sqlData;

    public function __construct($con, $input)
    {
        if (!$con) {
            throw new Exception("Database connection cannot be null");
        }

        $this->con = $con;
        $this->loadEntityData($input);
    }

    private function loadEntityData($input)
    {
        if (is_array($input)) {
            $this->sqlData = $input;
        } else {
            $query = $this->con->prepare("SELECT * FROM entities WHERE id = :id LIMIT 1");
            $query->bindValue(":id", (int)$input, PDO::PARAM_INT);

            if (!$query->execute()) {
                throw new Exception("Failed to load entity data");
            }

            $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
        }

        if (empty($this->sqlData)) {
            throw new Exception("Entity not found");
        }
    }

    // ... rest of the methods with input validation and output escaping ...

    public function getSeasons()
    {
        $query = $this->con->prepare("SELECT * FROM videos 
                                    WHERE entityId = :entityId AND isMovie = 0 
                                    ORDER BY season, episode ASC");
        $query->bindValue(":entityId", $this->getId(), PDO::PARAM_INT);

        if (!$query->execute()) {
            throw new Exception("Failed to load seasons");
        }

        $seasons = [];
        $currentSeason = null;
        $videos = [];

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            if ($currentSeason !== null && $currentSeason !== $row["season"]) {
                $seasons[] = new Season($currentSeason, $videos);
                $videos = [];
            }

            $currentSeason = (int)$row["season"];
            $videos[] = new Video($this->con, $row);
        }

        if (!empty($videos) && $currentSeason !== null) {
            $seasons[] = new Season($currentSeason, $videos);
        }

        return $seasons;
    }
}