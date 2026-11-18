-- Construction Site Materials & Equipment Tracking Information System
-- MySQL 8 / MariaDB — import in phpMyAdmin, or run setup.php (which runs this file).
-- ERD entities: users, materials, material_transactions, equipment,
-- equipment_assignments, maintenance_records.

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('project_manager','storekeeper','equipment_manager') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'General',
  unit VARCHAR(20) NOT NULL DEFAULT 'units',
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  reorder_level INT NOT NULL DEFAULT 10,
  current_stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS material_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  material_id INT NOT NULL,
  type ENUM('received','issued') NOT NULL,
  quantity INT NOT NULL CHECK (quantity > 0),
  issued_to VARCHAR(100) NULL,
  supplier VARCHAR(100) NULL,
  reference_no VARCHAR(60) NULL,
  transaction_date DATE NOT NULL,
  recorded_by INT NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
  FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS equipment (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'General',
  serial_number VARCHAR(60) NOT NULL UNIQUE,
  status ENUM('available','assigned','under_maintenance','out_of_service') NOT NULL DEFAULT 'available',
  location VARCHAR(120) NOT NULL DEFAULT 'Main Store',
  assigned_to VARCHAR(100) NULL,
  assigned_date DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS equipment_assignments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipment_id INT NOT NULL,
  assigned_to VARCHAR(100) NOT NULL,
  location VARCHAR(120) NOT NULL,
  assigned_date DATE NOT NULL,
  returned_date DATE NULL,
  status ENUM('active','returned') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS maintenance_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  equipment_id INT NOT NULL,
  maintenance_type ENUM('routine','repair','inspection') NOT NULL DEFAULT 'routine',
  description TEXT NOT NULL,
  maintenance_date DATE NOT NULL,
  cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  performed_by VARCHAR(100) NULL,
  next_due_date DATE NULL,
  status ENUM('completed','scheduled','in_progress') NOT NULL DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- Sample data (proposal examples: cement, sand, steel, timber; generators, excavators, mixers)
INSERT IGNORE INTO materials (id, name, category, unit, unit_price, reorder_level, current_stock) VALUES
(1,'Cement (50kg bag)','Cement','bags',850.00,100,240),
(2,'River Sand','Aggregates','tonnes',1800.00,20,35),
(3,'Steel Reinforcement Bars (12mm)','Steel','pieces',950.00,50,120),
(4,'Timber (2x4)','Timber','pieces',450.00,40,28),
(5,'Concrete Blocks','Masonry','pieces',65.00,200,520),
(6,'Ballast','Aggregates','tonnes',1600.00,15,18);

INSERT IGNORE INTO equipment (id, name, category, serial_number, status, location, assigned_to, assigned_date) VALUES
(1,'Concrete Mixer 350L','Mixing','MX-001','assigned','Block A — Slab','Site Team A','2026-11-10'),
(2,'Diesel Generator 10kVA','Power','GN-002','available','Main Store',NULL,NULL),
(3,'Excavator CAT 320','Earthworks','EX-003','under_maintenance','Workshop',NULL,NULL),
(4,'Tower Crane','Lifting','TC-004','assigned','Tower Section','Crane Crew','2026-11-05'),
(5,'Water Pump 3-inch','Pumping','WP-005','available','Main Store',NULL,NULL);

INSERT IGNORE INTO material_transactions (material_id, type, quantity, issued_to, supplier, transaction_date, notes) VALUES
(1,'received',300,NULL,'Bamburi Cement Ltd','2026-11-12','Delivery note DN-101'),
(1,'issued',60,'Site Team A',NULL,'2026-11-14','Slab works — Block A'),
(2,'received',40,NULL,'Savanna Aggregates','2026-11-11',''),
(3,'received',150,NULL,'Steel Masters Ltd','2026-11-10',''),
(4,'issued',22,'Carpentry Team',NULL,'2026-11-13','Formwork');

INSERT IGNORE INTO maintenance_records (equipment_id, maintenance_type, description, maintenance_date, cost, performed_by, next_due_date, status) VALUES
(3,'repair','Hydraulic hose replacement and oil change','2026-11-15',45000.00,'Brian — Equipment Mgr','2027-02-15','in_progress'),
(2,'routine','Oil, filters and general service','2026-11-08',12000.00,'Brian — Equipment Mgr','2027-02-08','completed'),
(1,'inspection','Drum and motor safety inspection','2026-11-09',0.00,'Brian — Equipment Mgr','2026-12-09','completed');
