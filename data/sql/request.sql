drop table if exists `request`;
create table `request` (
  `id` integer not null auto_increment,
  `app_id` integer not null,
  `package_id` integer default null,
  `number` integer not null comment 'アプリ毎の通し番号',
  `mail` varchar(255) not null comment 'リクエストした人',
  `message` text not null,
  `device_udid` varchar(36) not null comment 'リクエストした端末のUDID',
  `created` datetime not null,
  key idx_app (`app_id`),
  key idx_pkg (`package_id`),
  primary key (`id`)
)Engine=InnoDB default charset=utf8mb4;
