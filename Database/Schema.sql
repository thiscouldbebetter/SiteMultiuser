drop database Store;
create database Store;
use Store;

create table Product (ProductID int not null auto_increment, Name text not null, ImagePath text not null, Price decimal not null, ContentPath text not null, primary key (ProductID));

create table User (UserID int not null auto_increment, Username text not null, EmailAddress text not null, PasswordSalt text not null, PasswordHashed text not null, PasswordResetCode text, IsActive boolean not null, primary key (UserID) );

create table LicenseTransferType (LicenseTransferTypeID int not null, Name text not null, Description text not null, primary key (LicenseTransferTypeID) );
insert into LicenseTransferType (LicenseTransferTypeID, Name, Description) values (1, 'Username', 'To an Existing User by Username');
insert into LicenseTransferType (LicenseTransferTypeID, Name, Description) values (2, 'EmailAddress', 'To a Future or Existing User by Email Address');
insert into LicenseTransferType (LicenseTransferTypeID, Name, Description) values (3, 'TransferCode', 'To an Anonymous Recipient by Transfer Code');

create table License (LicenseID int not null auto_increment, UserID int not null, ProductID int not null, TransferTypeID int, TransferTarget text, primary key (LicenseID), foreign key (UserID) references User(UserID), foreign key (ProductID) references Product(ProductID), foreign key (TransferTypeID) references LicenseTransferType(LicenseTransferTypeID) );

/* "Order" is a SQL keyword, but no other name really makes sense, so prefix an "_". */
create table _Order (OrderID int not null auto_increment, UserID int not null, PromotionID int, Status text not null, TimeStarted datetime not null, TimeUpdated datetime not null, TimeCompleted datetime, PaymentID text, primary key (OrderID), foreign key (UserID) references User(UserID) );

create table Order_Product (OrderProductID int not null auto_increment, OrderID int, ProductID int, Quantity int, primary key (OrderProductID), foreign key (OrderID) references _Order(OrderID) );

create table Notification (NotificationID int not null auto_increment, Addressee text not null, Subject text not null, Body text not null, TimeCreated datetime not null, TimeSent datetime, primary key (NotificationID) );

create table Session(SessionID int not null auto_increment, UserID int, DeviceAddress text not null, TimeStarted datetime not null, TimeUpdated datetime not null, TimeEnded datetime, primary key (SessionID), foreign key (UserID) references User(UserID) );

create table PaypalClientData (ClientIDSandbox text not null, ClientSecretSandbox text not null, ClientIDProduction text not null, ClientSecretProduction text not null, IsProductionEnabled boolean not null);

create table Promotion (PromotionID int not null auto_increment, Description text not null, Discount decimal not null, Code text not null, primary key (PromotionID) );
create table Promotion_Product (PromotionProductID int not null auto_increment, PromotionID int not null, ProductID int not null, primary key (PromotionProductID), foreign key (PromotionID) references Promotion(PromotionID), foreign key (ProductID) references Product(ProductID) );
