BEGIN TRANSACTION;
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
	`id`	INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`username`	VARCHAR(255) NOT NULL,
	`email`	VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL
);
INSERT INTO `users` (id,username,email,password) VALUES (1,'admin','admin@example.com','$2y$10$s9gsxOirPGGozNCg3x3TmeRsdybwOcIqxg71ZOLdYdqLZDCx54lZC');
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
	`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
	`completed` BOOLEAN DEFAULT '0' NOT NULL, 
	`edited` BOOLEAN DEFAULT '0' NOT NULL, 
	`email`	VARCHAR(255) DEFAULT '' NOT NULL,
	`fullname`	VARCHAR(255) DEFAULT '' NOT NULL,
	`description` CLOB DEFAULT '' NOT NULL
);
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (1,1,1,'user1@example.com','user1','sample1');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (2,0,0,'user2@example.com','user2','sample2');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (3,0,1,'user3@example.com','user3','sample3');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (4,1,0,'user4@example.com','user4','sample4');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (5,0,0,'user5@example.com','user5','sample5');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (6,0,0,'user6@example.com','user6','sample6');
-- INSERT INTO `tasks` (id,completed,edited,email,fullname,description) VALUES (7,0,0,'user7@example.com','user7','sample7');
COMMIT;