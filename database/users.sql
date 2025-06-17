-- Create the users table if it doesn't exist
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(25) NOT NULL,
  `lastName` varchar(25) NOT NULL,
  `username` varchar(25) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(512) NOT NULL,
  `signUpDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `profilePic` varchar(500) DEFAULT 'assets/images/profilePictures/default.png',
  `isSubscribed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
