use Store;

grant select, update, insert, delete on *.* to 'web'@'localhost' identified by 'Password42';

/* ProductID, Name, ImagePath, Price, ContentPath, IsActive */
insert into Product select 1, 'Product01', '../Images/Red.png', 	1.00, '../Content/Product01/Content.php', true;
insert into Product select 2, 'Product02', '../Images/Orange.png', 	2.00, '../Content/Product02/Content.php', true;
insert into Product select 3, 'Product03', '../Images/Yellow.png', 	3.00, '../Content/Product03/Content.php', true;
insert into Product select 4, 'Product04', '../Images/Green.png', 	4.00, '../Content/Product04/Content.php', true;
insert into Product select 5, 'Product05', '../Images/Blue.png', 	5.00, '../Content/Product05/Content.php', true;
insert into Product select 6, 'Product06', '../Images/Violet.png', 	6.00, '../Content/Product06/Content.php', true;
insert into Product select 7, 'Product07', '../Images/Black.png', 	7.00, '', true;
insert into Product select 8, 'Product08', '../Images/Gray.png', 	0.00, '', true;
insert into Product select 9, 'Product09', '../Images/White.png', 	8.00, '', true;

insert into Product select 10, 'Product10', '../Images/Red.png', 	1.00, '', true;
insert into Product select 11, 'Product11', '../Images/Orange.png', 2.00, '', true;
insert into Product select 12, 'Product12', '../Images/Yellow.png', 3.00, '', true;
insert into Product select 13, 'Product13', '../Images/Green.png', 	4.00, '', true;
insert into Product select 14, 'Product14', '../Images/Blue.png', 	5.00, '', true;
insert into Product select 15, 'Product15', '../Images/Violet.png', 6.00, '', true;
insert into Product select 16, 'Product16', '../Images/Black.png', 	7.00, '', true;
insert into Product select 17, 'Product17', '../Images/Gray.png', 	0.00, '', true;
insert into Product select 18, 'Product18', '../Images/White.png', 	8.00, '', true;
insert into Product select 19, 'Product19', '../Images/Brown.png', 	9.00, '', true;

insert into Product select 20, 'Product20', '../Images/Red.png', 	1.00, '', true;
insert into Product select 21, 'Product21', '../Images/Orange.png', 2.00, '', true;
insert into Product select 22, 'Product22', '../Images/Yellow.png', 3.00, '', true;
insert into Product select 23, 'Product23', '../Images/Green.png', 	4.00, '', true;
insert into Product select 24, 'Product24', '../Images/Blue.png', 	5.00, '', true;
insert into Product select 25, 'Product25', '../Images/Violet.png', 6.00, '', true;
insert into Product select 26, 'Product26', '../Images/Black.png', 	7.00, '', true;
insert into Product select 27, 'Product27', '../Images/Gray.png', 	0.00, '', true;
insert into Product select 28, 'Product28', '../Images/White.png', 	8.00, '', true;
insert into Product select 29, 'Product29', '../Images/Brown.png', 	9.00, '', true;

insert into Product select 30, 'Product30', '../Images/Red.png', 	1.00, '', true;
insert into Product select 31, 'Product31', '../Images/Orange.png', 2.00, '', true;
insert into Product select 32, 'Product32', '../Images/Yellow.png', 3.00, '', true;
insert into Product select 33, 'Product33', '../Images/Green.png', 	4.00, '', true;
insert into Product select 34, 'Product34', '../Images/Blue.png', 	5.00, '', true;
insert into Product select 35, 'Product35', '../Images/Violet.png', 6.00, '', true;
insert into Product select 36, 'Product36', '../Images/Black.png', 	7.00, '', true;
insert into Product select 37, 'Product37', '../Images/Gray.png', 	0.00, '', true;
insert into Product select 38, 'Product38', '../Images/White.png', 	8.00, '', true;
insert into Product select 39, 'Product39', '../Images/Brown.png', 	9.00, '', true;

/* An inactive product test. */
insert into Product select 99, 'Product99', '../Images/Gray.png', 	99.99, '', false;

insert into Promotion (Description, Discount, Code) values ('Save $1 on Product1 when you buy Product2.', 1.00, '12341234-1234-1234-1234-123412341234');
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
