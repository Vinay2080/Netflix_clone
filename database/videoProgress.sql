-- Create the videoProgress table if it doesn't exist
CREATE TABLE IF NOT EXISTS `videoProgress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `videoId` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0,
  `dateModified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `finished` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_video` (`username`, `videoId`),
  KEY `videoId` (`videoId`),
  CONSTRAINT `fk_videoprogress_video` FOREIGN KEY (`videoId`) REFERENCES `videos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
