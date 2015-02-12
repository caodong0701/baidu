CREATE TABLE `bizs` (
  `id` int(11) NOT NULL auto_increment,
  `city` varchar(16) not NULL default '',
  `name` varchar(64) not NULL default '',
  `address` varchar(128) not NULL default '',
  `telephone` varchar(128) not NULL default '',
  `lat` int(11) not null default 0,
  `lng` int(11) not null default 0,
  `type` varchar(16) not NULL default '',
  `tag` varchar(32) not NULL default '',
  `uid` varchar(32) not NULL default '',
  `create_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`uid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

alter table bizs add `city` varchar(16) not NULL default '' after id;
alter table bizs add `area_id` int(11) not NULL default '' after name;
alter table bizs add `area` varchar(16) not NULL default '' after area_id;
alias
shop_hours
image
status

overall_rating
taste_rating
service_rating
environment_rating



CREATE TABLE `uids` (
  `uid` varchar(32) not NULL,
  `state` tinyint(4) default '0',
  `update_time` timestamp,
  PRIMARY KEY  (`uid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE `imgs` (
  `uid` varchar(32) not NULL,
  `state` tinyint(4) default '0',
  `update_time` timestamp,
  PRIMARY KEY  (`uid`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;




replace into bizs(uid,name,address,telephone,lat,lng,type,tag) values('75d0881a94e433543b04b1f5','海底捞望京店','北京朝阳区望京街9号望京国际商业中心四层','(010)59203512','39000000','116000000','cater','火锅,美食,其他火锅,望京');
