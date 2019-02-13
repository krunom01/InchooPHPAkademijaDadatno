DROP DATABASE IF EXISTS social_network;
CREATE DATABASE social_network CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use social_network;

create table user(
id int not null primary key auto_increment,
firstname varchar(50) not null,
lastname varchar(50) not null,
email varchar(100) not null,
pass char(60) not null,
admin varchar(10),
image LONGBLOB
)engine=InnoDB;

create unique index ix1 on user(email);


create table post(
id int not null primary key auto_increment,
content text,
user int not null,
date datetime not null default now(),
hidden varchar(10)
)engine=InnoDB;

create table comment(
id int not null primary key auto_increment,
user int not null,
post int not null,
content text not null,
date datetime not null default now()
)engine=InnoDB;

create table likes(
id int not null primary key auto_increment,
user int not null,
post int not null,
uniquelike varchar(25) not null
)engine=InnoDB;
create unique index ix2 on likes(uniquelike);

create table report(
id_report int not null primary key auto_increment,
user_report int not null,
post_report int not null,
uniquelike_report varchar(25) not null
)engine=InnoDB;
create unique index ix3 on report(uniquelike_report);

create table tag(
id int not null primary key auto_increment,
content text not null
)engine=InnoDB;

create table tag_post(
post int not null,
tag int not null
)engine=InnoDB;

create table commentlikes(
id_clike int not null primary key auto_increment,
user_clike int not null,
post_clike int not null,
uniqueclike varchar(25) not null
)engine=InnoDB;
create unique index ix3 on commentlikes(uniqueclike);


create table reportComment(
id_rc int not null primary key auto_increment,
user_rc int not null,
post_rc int not null,
uniquerc varchar(25) not null
)engine=InnoDB;
create unique index ix4 on reportComment(uniquerc);






alter table post add FOREIGN KEY (user) REFERENCES user(id);

alter table comment add FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE;
alter table comment add FOREIGN KEY (post) REFERENCES post(id) ON DELETE CASCADE;

alter table likes add FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE;
alter table likes add FOREIGN KEY (post) REFERENCES post(id) ON DELETE CASCADE;

alter table report add FOREIGN KEY (user_report) REFERENCES user(id) ON DELETE CASCADE;
alter table report add FOREIGN KEY (post_report) REFERENCES post(id) ON DELETE CASCADE;

alter table commentlikes add FOREIGN KEY (user_clike) REFERENCES user(id) ON DELETE CASCADE;
alter table commentlikes add FOREIGN KEY (post_clike) REFERENCES comment(id) ON DELETE CASCADE;

alter table reportComment add FOREIGN KEY (user_rc) REFERENCES user(id) ON DELETE CASCADE;
alter table reportComment add FOREIGN KEY (post_rc) REFERENCES comment(id) ON DELETE CASCADE;


alter table tag_post add FOREIGN KEY (post) REFERENCES post(id) ON DELETE CASCADE;
alter table tag_post add FOREIGN KEY(tag) references tag(id) ON DELETE CASCADE;





insert into user (id,firstname,lastname,email,pass,admin) values
(null,'Tomislav','Jakopec','tjakopec@gmail.com','$2y$10$LFXuW6y.P0Zd81fwd..CK.pCd6ZcoT5DsY7rqet9jwzReaoRi7yua','admin');

insert into user (id,firstname,lastname,email,pass,admin) values
(null,'Mara','Jakopec','mjakopec@gmail.com','$2y$10$LFXuW6y.P0Zd81fwd..CK.pCd6ZcoT5DsY7rqet9jwzReaoRi7yua','user');



