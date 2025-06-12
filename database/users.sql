create
database users;

CREATE TABLE `users`
(
    `id`           int(11)      NOT NULL AUTO_INCREMENT,
    `username`     varchar(25)  NOT NULL,
    `firstName`    varchar(50)  NOT NULL,
    `lastName`     varchar(50)  NOT NULL,
    `email`        varchar(100) NOT NULL,
    `password`     varchar(255) NOT NULL,
    `signUpDate`   datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `profilePic`   varchar(255)          DEFAULT 'assets/images/profilePictures/default.png',
    `isSubscribed` tinyint(1)            DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;