drop database Store;
create database Store;
use Store;

create table Product (ProductID int not null auto_increment, Name text not null, ImagePath text not null, Price decimal not null, primary key (ProductID) );

create table User (UserID int not null auto_increment, Username text not null, EmailAddress text not null, PasswordSalt text not null, PasswordHashed text not null, PasswordResetCode text, IsActive boolean not null, primary key (UserID) );

create table License (LicenseID int not null auto_increment, UserID int not null, ProductID int not null, primary key (LicenseID), foreign key (UserID) references User(UserID), foreign key (ProductID) references Product(ProductID) );

/* "Order" is a SQL keyword, but no other name really makes sense, so prefix an "_". */
create table _Order (OrderID int not null auto_increment, UserID int not null, Status text not null, TimeCompleted datetime, primary key (OrderID), foreign key (UserID) references User(UserID) );

create table Order_Product (OrderProductID int not null auto_increment, OrderID int, ProductID int, Quantity int, primary key (OrderProductID), foreign key (OrderID) references _Order(OrderID) );

create table Notification (NotificationID int not null auto_increment, Addressee text not null, Subject text not null, Body text not null, primary key (NotificationID) );

create table Session(SessionID int not null auto_increment, UserID int, SessionToken text not null, TimeStarted datetime not null, TimeUpdated datetime not null, TimeEnded datetime, primary key (SessionID), foreign key (UserID) references User(UserID) );

create table PaypalClientData (ClientIDSandbox text not null, ClientIDProduction text not null, IsProductionEnabled boolean not null);
