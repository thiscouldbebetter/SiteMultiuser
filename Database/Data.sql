use Store;

grant select, update, insert, delete on *.* to 'web'@'localhost' identified by 'Password42';

insert into Product (Name, ImagePath, Price, ContentPath) values ('Product01', "../Images/Red.png", 	1.00, "../Content/Product01/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product02', "../Images/Orange.png", 	2.00, "../Content/Product02/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product03', "../Images/Yellow.png", 	3.00, "../Content/Product03/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product04', "../Images/Green.png", 	4.00, "../Content/Product04/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product05', "../Images/Blue.png", 	5.00, "../Content/Product05/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product06', "../Images/Violet.png", 	6.00, "../Content/Product06/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product07', "../Images/Black.png", 	7.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product08', "../Images/Gray.png", 	0.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product09', "../Images/White.png", 	8.00, "");

insert into Product (Name, ImagePath, Price, ContentPath) values ('Product10', "../Images/Red.png", 	1.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product11', "../Images/Orange.png", 	2.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product12', "../Images/Yellow.png", 	3.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product13', "../Images/Green.png", 	4.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product14', "../Images/Blue.png", 	5.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product15', "../Images/Violet.png", 	6.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product16', "../Images/Black.png", 	7.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product17', "../Images/Gray.png", 	0.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product18', "../Images/White.png", 	8.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product19', "../Images/Brown.png", 	9.00, "");

insert into Product (Name, ImagePath, Price, ContentPath) values ('Product20', "../Images/Red.png", 	1.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product21', "../Images/Orange.png", 	2.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product22', "../Images/Yellow.png", 	3.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product23', "../Images/Green.png", 	4.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product24', "../Images/Blue.png", 	5.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product25', "../Images/Violet.png", 	6.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product26', "../Images/Black.png", 	7.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product27', "../Images/Gray.png", 	0.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product28', "../Images/White.png", 	8.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product29', "../Images/Brown.png", 	9.00, "");

insert into Product (Name, ImagePath, Price, ContentPath) values ('Product30', "../Images/Red.png", 	1.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product31', "../Images/Orange.png", 	2.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product32', "../Images/Yellow.png", 	3.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product33', "../Images/Green.png", 	4.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product34', "../Images/Blue.png", 	5.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product35', "../Images/Violet.png", 	6.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product36', "../Images/Black.png", 	7.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product37', "../Images/Gray.png", 	0.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product38', "../Images/White.png", 	8.00, "");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product39', "../Images/Brown.png", 	9.00, "");

insert into Promotion (Description, Discount, Code) values ('Save $1 on Product1 when you buy Product2.', 1.00, "12341234-1234-1234-1234-123412341234");
insert into Promotion_Product (PromotionID, ProductID) values (1, 1);
insert into Promotion_Product (PromotionID, ProductID) values (1, 2);

/* user "adam", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('adam', 'adam@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);

insert into _Order (UserID, PromotionID, Status, TimeStarted, TimeUpdated, TimeCompleted, PaymentID) values (1, null, 'Completed', '2015-01-01', '2015-01-02', '2015-01-02', null);
insert into Order_Product (OrderID, ProductID, Quantity) values (1, 2, 1);
insert into Order_Product (OrderID, ProductID, Quantity) values (1, 3, 1);
insert into Order_Product (OrderID, ProductID, Quantity) values (1, 4, 1);

insert into License (UserID, ProductID) values (1, 1);
insert into License (UserID, ProductID, TransferTypeID, TransferTarget) values (1, 2, 1, 'beth');
insert into License (UserID, ProductID, TransferTypeID, TransferTarget) values (1, 3, 2, 'charlie@localhost.localdomain');
insert into License (UserID, ProductID, TransferTypeID, TransferTarget) values (1, 4, 2, '12341234-1234-1234-1234-123412341234');

/* user "beth", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('beth', 'beth@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
insert into License (UserID, ProductID) values (2, 1);

/* user "charlie", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('charlie', 'charlie@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
insert into License (UserID, ProductID) values (3, 1);

/* user "diane", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('diane', 'diane@localhost.localdomain', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
insert into License (UserID, ProductID) values (3, 1);
