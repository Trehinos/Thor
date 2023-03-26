INSERT INTO `user` (public_id, username, hash, permissions, parameters)
VALUES (HEX(RANDOM_BYTES(16)), 'admin', PASSWORD('password'), '{}', '{}');
