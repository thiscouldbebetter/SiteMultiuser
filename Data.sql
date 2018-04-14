use Store;

insert into PaypalClientData (ClientIDSandbox, ClientIDProduction, IsProductionEnabled) values ('[redacted]', '[redacted]', 0);

grant select, update, insert, delete on *.* to 'web'@'localhost' identified by '[redacted]';

insert into Product (Name, ImagePath, Price) values ('Product1', "Images/Product1.png", 1.00);
insert into Product (Name, ImagePath, Price) values ('Product2', "Images/Product2.png", 2.00);
insert into Product (Name, ImagePath, Price) values ('Product3', "Images/Product3.png", 3.00);

/* user "joe", password "Password_123" */
insert into User (Username, EmailAddress, PasswordSalt, PasswordHashed, PasswordResetCode, IsActive) values ('joe', 'joe@localhost', '1147748628', '874c1d861559fa124a3948a947bc1f6564ea478b56e37b976a9ad25bbd67092e', null, 1);
insert into License (UserID, ProductID) values (1, 1);

