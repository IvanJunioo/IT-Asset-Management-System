USE itam;

-- --------------------------------------------------------
-- Employees
-- --------------------------------------------------------
INSERT INTO employee (EmpID, EmpMail, FName, LName, Privilege, ActiveStatus) VALUES
('EMP00001', 'alice@example.com', 'Alice', 'Smith', 'Admin', 'Active'),
('EMP00002', 'bob@example.com', 'Bob', 'Johnson', 'Faculty', 'Active'),
('EMP00003', 'carol@example.com', 'Carol', 'Lee', 'SuperAdmin', 'Active'),
('EMP00004', 'dave@example.com', 'Dave', 'Brown', 'Faculty', 'Inactive'),
('EMP00005', 'eve@example.com', 'Eve', 'White', 'Faculty', 'Active'),
('EMP00006', 'frank@example.com', 'Frank', 'Green', 'Admin', 'Active'),
('EMP00007', 'grace@example.com', 'Grace', 'Hall', 'Faculty', 'Active'),
('EMP00008', 'henry@example.com', 'Henry', 'King', 'Admin', 'Inactive'),
('EMP00009', 'ivy@example.com', 'Ivy', 'Scott', 'SuperAdmin', 'Active'),
('EMP00010', 'jack@example.com', 'Jack', 'Adams', 'Faculty', 'Active');

-- --------------------------------------------------------
-- Employee contacts
-- --------------------------------------------------------
INSERT INTO empcontact (EmpID, ContactNum) VALUES
('EMP00001','09170000001'),
('EMP00002','09170000002'),
('EMP00003','09170000003'),
('EMP00004','09170000004'),
('EMP00005','09170000005'),
('EMP00006','09170000006'),
('EMP00007','09170000007'),
('EMP00008','09170000008'),
('EMP00009','09170000009'),
('EMP00010','09170000010');

-- --------------------------------------------------------
-- Assets
-- --------------------------------------------------------
INSERT INTO asset (PropNum, SerialNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL) VALUES
('PROP00000001','SN0000000001','2025-12-01','Laptop, i7, 16GB RAM, 512GB SSD','New laptop','Unassigned','Office Laptop',1200.00,'https://example.com/laptop1'),
('PROP00000002','SN0000000002','2025-12-02','Monitor, 27 inch, 4K','New monitor','Unassigned','Office Monitor',350.00,'https://example.com/monitor2'),
('PROP00000003','SN0000000003','2025-12-03','Projector, 4000 lumens','Conference room projector','ToCondemn','Meeting Projector',800.00,'https://example.com/projector3'),
('PROP00000004','SN0000000004','2025-12-04','Router, Gigabit','Main router','Unassigned','Network Router',200.00,'https://example.com/router4'),
('PROP00000005','SN0000000005','2025-12-05','Switch, 24 ports','New switch','Unassigned','Network Switch',300.00,'https://example.com/switch5'),
('PROP00000006','SN0000000006','2025-12-06','Printer, Laser','Office printer','Assigned','Laser Printer',250.00,'https://example.com/printer6'),
('PROP00000007','SN0000000007','2025-12-07','Scanner, A4','New scanner','Unassigned','Document Scanner',150.00,'https://example.com/scanner7'),
('PROP00000008','SN0000000008','2025-12-08','Laptop, i5, 8GB RAM, 256GB SSD','Backup laptop','Assigned','Backup Laptop',900.00,'https://example.com/laptop8'),
('PROP00000009','SN0000000009','2025-12-09','Monitor, 24 inch','Secondary monitor','Unassigned','Secondary Monitor',200.00,'https://example.com/monitor9'),
('PROP00000010','SN0000000010','2025-12-10','Projector, 3000 lumens','Backup projector','Unassigned','Backup Projector',600.00,'https://example.com/projector10'),
('PROP00000011','SN0000000011','2025-12-11','Laptop, i7, 32GB RAM, 1TB SSD','High-end laptop','Unassigned','High-End Laptop',1800.00,'https://example.com/laptop11'),
('PROP00000012','SN0000000012','2025-12-12','Monitor, 32 inch, 4K','Design monitor','Assigned','Design Monitor',500.00,'https://example.com/monitor12'),
('PROP00000013','SN0000000013','2025-12-13','Router, 10Gb','Backup router','Unassigned','Backup Router',400.00,'https://example.com/router13'),
('PROP00000014','SN0000000014','2025-12-14','Switch, 48 ports','Data center switch','Unassigned','Data Center Switch',800.00,'https://example.com/switch14'),
('PROP00000015','SN0000000015','2025-12-15','Printer, Color Laser','Design printer','Assigned','Color Printer',450.00,'https://example.com/printer15'),
('PROP00000016','SN0000000016','2025-12-16','Scanner, A3','Large format scanner','Unassigned','Large Scanner',300.00,'https://example.com/scanner16'),
('PROP00000017','SN0000000017','2025-12-17','Laptop, i9, 64GB RAM, 2TB SSD','Server laptop','Unassigned','Server Laptop',2500.00,'https://example.com/laptop17'),
('PROP00000018','SN0000000018','2025-12-18','Monitor, 34 inch ultrawide','Ultra monitor','Assigned','Ultra Monitor',700.00,'https://example.com/monitor18'),
('PROP00000019','SN0000000019','2025-12-19','Projector, 5000 lumens','Conference projector','Unassigned','Conference Projector',1200.00,'https://example.com/projector19'),
('PROP00000020','SN0000000020','2025-12-20','Laptop, i5, 16GB RAM, 512GB SSD','Staff laptop','Assigned','Staff Laptop',1000.00,'https://example.com/laptop20');

-- --------------------------------------------------------
-- Procurement Numbers
-- --------------------------------------------------------
INSERT INTO procurement (PropNum, ProcNum) VALUES
('PROP00000001','PRC000000001'),
('PROP00000002','PRC000000002'),
('PROP00000003','PRC000000003'),
('PROP00000004','PRC000000004'),
('PROP00000005','PRC000000005'),
('PROP00000006','PRC000000006'),
('PROP00000007','PRC000000007'),
('PROP00000008','PRC000000008'),
('PROP00000009','PRC000000009'),
('PROP00000010','PRC000000010'),
('PROP00000011','PRC000000011'),
('PROP00000012','PRC000000012'),
('PROP00000013','PRC000000013'),
('PROP00000014','PRC000000014'),
('PROP00000015','PRC000000015'),
('PROP00000016','PRC000000016'),
('PROP00000017','PRC000000017'),
('PROP00000018','PRC000000018'),
('PROP00000019','PRC000000019'),
('PROP00000020','PRC000000020');

-- --------------------------------------------------------
-- Assignments
-- --------------------------------------------------------
INSERT INTO assignment (PropNum, AssignDateTime, AssignerID, AssigneeID, ReturnDateTime, Remarks) VALUES
('PROP00000006','2026-01-01 09:00:00','EMP00003','EMP00001',NULL,'Assigned to Alice'),
('PROP00000008','2026-01-02 10:30:00','EMP00003','EMP00002',NULL,'Assigned to Bob'),
('PROP00000012','2026-01-03 11:00:00','EMP00001','EMP00005',NULL,'Assigned to Eve'),
('PROP00000015','2026-01-04 14:00:00','EMP00003','EMP00006',NULL,'Assigned to Frank'),
('PROP00000018','2026-01-05 15:00:00','EMP00001','EMP00007',NULL,'Assigned to Grace'),
('PROP00000020','2026-01-06 09:30:00','EMP00003','EMP00010',NULL,'Assigned to Jack');

-- --------------------------------------------------------
-- Activity logs
-- --------------------------------------------------------
INSERT INTO actlog (Timestamp, ActorID, Message, Metadata) VALUES
('2026-01-01 09:00:00','EMP00003','success!','{"action":"assign","object":"asset","propNum":"PROP00000006","assigneeID":"EMP00001"}'),
('2026-01-02 10:30:00','EMP00003','success!','{"action":"assign","object":"asset","propNum":"PROP00000008","assigneeID":"EMP00002"}'),
('2026-01-03 11:00:00','EMP00001','success!','{"action":"assign","object":"asset","propNum":"PROP00000012","assigneeID":"EMP00005"}'),
('2026-01-04 14:00:00','EMP00003','success!','{"action":"assign","object":"asset","propNum":"PROP00000015","assigneeID":"EMP00006"}'),
('2026-01-05 15:00:00','EMP00001','success!','{"action":"assign","object":"asset","propNum":"PROP00000018","assigneeID":"EMP00007"}'),
('2026-01-06 09:30:00','EMP00003','success!','{"action":"assign","object":"asset","propNum":"PROP00000020","assigneeID":"EMP00010"}');
