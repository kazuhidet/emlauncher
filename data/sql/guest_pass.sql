drop table if exists `guest_pass`;
CREATE TABLE `guest_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` datetime NOT NULL,
  `expired` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mail_app` (`mail`,`app_id`,`package_id`),
  KEY `idx_package` (`package_id`),
  KEY `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `guestpass_log`;
CREATE TABLE guestpass_log
(
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `guest_pass_id` INT NOT NULL,
  `user_agent` VARCHAR(1000) NOT NULL,
  `ip_address` VARCHAR(255) NOT NULL,
  `installed` DATETIME NOT NULL,
  KEY `idx_guest_pass_id` (`guest_pass_id`)
)Engine=InnoDB default charset=utf8mb4;
