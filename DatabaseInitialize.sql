drop database Store;
create database Store;
use Store;

create table Product (ProductID int not null auto_increment, Name text not null, primary key (ProductID) );
insert into Product (Name) values ('Product1');
insert into Product (Name) values ('Product2');
insert into Product (Name) values ('Product3');

create table User (UserID int not null auto_increment, Username text not null, EmailAddress text not null, PasswordSalt text not null, PasswordHashed text not null, IsActive boolean not null, primary key (UserID) );
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed) values ('test', 'nobody@null.net', 'todo', 'todo', 1);
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed) values ('admin2', 'nobody@null.net', 'todo', '4VrY9YERdH+f', 1);

create table User_Product (UserProductID int not null auto_increment, UserID int not null, ProductID int not null, primary key (UserProductID), foreign key (UserID) references User(UserID), foreign key (ProductID) references Product(ProductID) );
insert into User_Product (UserID, ProductID) values (1, 1);
