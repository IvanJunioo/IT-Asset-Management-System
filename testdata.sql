USE itam;

-- --------------------------------------------------------
-- Employees
-- --------------------------------------------------------
INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus) VALUES
('EMP00001', 'alice@example.com', 'Alice', 'M', 'Smith', 'Admin', 'Active'),
('EMP00002', 'bob@example.com', 'Bob', 'J', 'Johnson', 'Faculty', 'Active'),
('EMP00003', 'carol@example.com', 'Carol', 'K', 'Lee', 'SuperAdmin', 'Active'),
('EMP00004', 'dave@example.com', 'Dave', 'L', 'Brown', 'Faculty', 'Inactive'),
('EMP00005', 'eve@example.com', 'Eve', 'A', 'White', 'Faculty', 'Active'),
('EMP00006', 'frank@example.com', 'Frank', 'B', 'Green', 'Admin', 'Active'),
('EMP00007', 'grace@example.com', 'Grace', 'C', 'Hall', 'Faculty', 'Active'),
('EMP00008', 'henry@example.com', 'Henry', 'D', 'King', 'Admin', 'Inactive'),
('EMP00009', 'ivy@example.com', 'Ivy', 'E', 'Scott', 'SuperAdmin', 'Active'),
('EMP00010', 'jack@example.com', 'Jack', 'F', 'Adams', 'Faculty', 'Active');

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
INSERT INTO asset (PropNum, SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL) VALUES
('PROP000001','SN000001','PRC00001','2025-12-01','Laptop, i7, 16GB RAM, 512GB SSD','New laptop','Available','Office Laptop',1200.00,'https://example.com/laptop1'),
('PROP000002','SN000002','PRC00002','2025-12-02','Monitor, 27 inch, 4K','New monitor','Available','Office Monitor',350.00,'https://example.com/monitor2'),
('PROP000003','SN000003','PRC00003','2025-12-03','Projector, 4000 lumens','Conference room projector','InRepair','Meeting Projector',800.00,'https://example.com/projector3'),
('PROP000004','SN000004','PRC00004','2025-12-04','Router, Gigabit','Main router','Available','Network Router',200.00,'https://example.com/router4'),
('PROP000005','SN000005','PRC00005','2025-12-05','Switch, 24 ports','New switch','Available','Network Switch',300.00,'https://example.com/switch5'),
('PROP000006','SN000006','PRC00006','2025-12-06','Printer, Laser','Office printer','Assigned','Laser Printer',250.00,'https://example.com/printer6'),
('PROP000007','SN000007','PRC00007','2025-12-07','Scanner, A4','New scanner','Available','Document Scanner',150.00,'https://example.com/scanner7'),
('PROP000008','SN000008','PRC00008','2025-12-08','Laptop, i5, 8GB RAM, 256GB SSD','Backup laptop','Assigned','Backup Laptop',900.00,'https://example.com/laptop8'),
('PROP000009','SN000009','PRC00009','2025-12-09','Monitor, 24 inch','Secondary monitor','Available','Secondary Monitor',200.00,'https://example.com/monitor9'),
('PROP000010','SN000010','PRC00010','2025-12-10','Projector, 3000 lumens','Backup projector','Available','Backup Projector',600.00,'https://example.com/projector10'),
('PROP000011','SN000011','PRC00011','2025-12-11','Laptop, i7, 32GB RAM, 1TB SSD','High-end laptop','Available','High-End Laptop',1800.00,'https://example.com/laptop11'),
('PROP000012','SN000012','PRC00012','2025-12-12','Monitor, 32 inch, 4K','Design monitor','Assigned','Design Monitor',500.00,'https://example.com/monitor12'),
('PROP000013','SN000013','PRC00013','2025-12-13','Router, 10Gb','Backup router','Available','Backup Router',400.00,'https://example.com/router13'),
('PROP000014','SN000014','PRC00014','2025-12-14','Switch, 48 ports','Data center switch','Available','Data Center Switch',800.00,'https://example.com/switch14'),
('PROP000015','SN000015','PRC00015','2025-12-15','Printer, Color Laser','Design printer','Assigned','Color Printer',450.00,'https://example.com/printer15'),
('PROP000016','SN000016','PRC00016','2025-12-16','Scanner, A3','Large format scanner','Available','Large Scanner',300.00,'https://example.com/scanner16'),
('PROP000017','SN000017','PRC00017','2025-12-17','Laptop, i9, 64GB RAM, 2TB SSD','Server laptop','Available','Server Laptop',2500.00,'https://example.com/laptop17'),
('PROP000018','SN000018','PRC00018','2025-12-18','Monitor, 34 inch ultrawide','Ultra monitor','Assigned','Ultra Monitor',700.00,'https://example.com/monitor18'),
('PROP000019','SN000019','PRC00019','2025-12-19','Projector, 5000 lumens','Conference projector','Available','Conference Projector',1200.00,'https://example.com/projector19'),
('PROP000020','SN000020','PRC00020','2025-12-20','Laptop, i5, 16GB RAM, 512GB SSD','Staff laptop','Assigned','Staff Laptop',1000.00,'https://example.com/laptop20');

-- --------------------------------------------------------
-- Assignments
-- --------------------------------------------------------
INSERT INTO assignment (PropNum, AssignDateTime, AssignerID, AssigneeID, ReturnDateTime, Remarks) VALUES
('PROP000006','2026-01-01 09:00:00','EMP00003','EMP00001',NULL,'Assigned to Alice'),
('PROP000008','2026-01-02 10:30:00','EMP00003','EMP00002',NULL,'Assigned to Bob'),
('PROP000012','2026-01-03 11:00:00','EMP00001','EMP00005',NULL,'Assigned to Eve'),
('PROP000015','2026-01-04 14:00:00','EMP00003','EMP00006',NULL,'Assigned to Frank'),
('PROP000018','2026-01-05 15:00:00','EMP00001','EMP00007',NULL,'Assigned to Grace'),
('PROP000020','2026-01-06 09:30:00','EMP00003','EMP00010',NULL,'Assigned to Jack');

-- --------------------------------------------------------
-- Activity logs
-- --------------------------------------------------------
INSERT INTO actlog (Timestamp, ActorID, Log, Metadata) VALUES
('2026-01-01 09:00:00','EMP00003','Assigned PROP000006 to EMP00001','{"PropNum":"PROP000006","Assignee":"EMP00001"}'),
('2026-01-02 10:30:00','EMP00003','Assigned PROP000008 to EMP00002','{"PropNum":"PROP000008","Assignee":"EMP00002"}'),
('2026-01-03 11:00:00','EMP00001','Assigned PROP000012 to EMP00005','{"PropNum":"PROP000012","Assignee":"EMP00005"}'),
('2026-01-04 14:00:00','EMP00003','Assigned PROP000015 to EMP00006','{"PropNum":"PROP000015","Assignee":"EMP00006"}'),
('2026-01-05 15:00:00','EMP00001','Assigned PROP000018 to EMP00007','{"PropNum":"PROP000018","Assignee":"EMP00007"}'),
('2026-01-06 09:30:00','EMP00003','Assigned PROP000020 to EMP00010','{"PropNum":"PROP000020","Assignee":"EMP00010"}');

