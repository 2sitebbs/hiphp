/* db schema for demo */
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`(
	id int not null auto_increment,
	username varchar(30) not null,
	primary key (`id`)
)DEFAULT CHARSET=utf8;


/* test data */
insert into user(username) values('test'),('admin');