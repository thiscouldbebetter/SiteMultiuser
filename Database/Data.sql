use Store;

insert into PaypalClientData (ClientIDSandbox, ClientSecretSandbox, ClientIDProduction, ClientSecretProduction, IsProductionEnabled) values ('[redacted]', '[redacted]', '[redacted]', '[redacted]', 0);

grant select, update, insert, delete on *.* to 'web'@'localhost' identified by '[redacted]';

insert into Product (Name, ImagePath, Price, ContentPath) values ('Product1', "Images/Product1.png", 1.00, "Content/Product1/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product2', "Images/Product2.png", 2.00, "Content/Product2/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product3', "Images/Product3.png", 3.00, "Content/Product3/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product4', "Images/Product4.png", 4.00, "Content/Product4/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product5', "Images/Product5.png", 5.00, "Content/Product5/Content.php");
insert into Product (Name, ImagePath, Price, ContentPath) values ('Product6', "Images/Product6.png", 6.00, "Content/Product6/Content.php");

insert into Promotion (Description, Discount, Code) values ('Save $0 on Product1 when you buy Product2.', 0.00, "12341234-1234-1234-1234-123412341234");
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
