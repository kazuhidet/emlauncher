drop table if exists `ios_device_info`;
CREATE TABLE `ios_device_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) NOT NULL,
  `device_uuid` varchar(36) NOT NULL UNIQUE,
  `device_udid` varchar(40) DEFAULT NULL UNIQUE,
  `device_name` varchar(64) DEFAULT NULL,
  `device_version` varchar(32) DEFAULT NULL,
  `device_product` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_mail` (`mail`),
  KEY `idx_device_uuid` (`device_uuid`),
  KEY `idx_device_udid` (`device_udid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

