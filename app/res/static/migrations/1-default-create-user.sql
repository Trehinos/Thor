CREATE TABLE `user`
(
    `public_id`   varchar(255)  NOT NULL,
    `id`          int(10) NOT NULL AUTO_INCREMENT,
    `username`    varchar(255)  NOT NULL,
    `hash`        varchar(255)  NOT NULL,
    `permissions` JSON NOT NULL,
    `parameters`  JSON NOT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_public_id` (`public_id`),
    UNIQUE KEY `uniq_username` (`username`)
) DEFAULT CHARSET=utf8mb4;



