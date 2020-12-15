drop table if exists `package_udid`;
CREATE TABLE `package_udid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `device_udid` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_package_id` (`package_id`),
  KEY `idx_device_udid` (`device_udid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

