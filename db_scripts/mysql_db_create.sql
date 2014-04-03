CREATE DATABASE dedc;
USE dedc;

# Create listing of all types
CREATE TABLE types
(
type VARCHAR(20) NOT NULL,
PRIMARY KEY (type)
)Engine InnoDB;

# Insert valid types into types table
INSERT INTO types(type) VALUES('Process');
INSERT INTO types(type) VALUES('DataStore');
INSERT INTO types(type) VALUES('Multiprocess');
INSERT INTO types(type) VALUES('ExternalInteractor');
INSERT INTO types(type) VALUES('DataFlow');
INSERT INTO types(type) VALUES('DataFlowDiagram');

CREATE TABLE locks
(
entityId CHAR(44) NOT NULL,
lockTime DATETIME NOT NULL,
PRIMARY KEY (entityId)
) Engine InnoDB;

CREATE TABLE users
(
id CHAR(44) NOT NULL,
userName CHAR(100) NOT NULL,
organization CHAR(100) NOT NULL,
admin BIT NOT NULL,
PRIMARY KEY (id),
UNIQUE userOrg (userName, organization)
) Engine InnoDB;

CREATE TABLE entity
(
id CHAR(44) NOT NULL,
label VARCHAR(100),
type VARCHAR(20) NOT NULL,
userId CHAR(44) NOT NULL,
PRIMARY KEY (id),
FOREIGN KEY (type)
REFERENCES types(type),
FOREIGN KEY (userId)
REFERENCES users(id)
)Engine InnoDB;

CREATE TABLE element
(
id CHAR(44) NOT NULL,
x INT,
y INT,
PRIMARY KEY (id),
FOREIGN KEY (id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE link 
( 
id CHAR(44) NOT NULL, 
originNode CHAR(44), 
destinationNode CHAR(44), 
PRIMARY KEY (id), 
FOREIGN KEY (id) 
REFERENCES entity(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE node
(
id CHAR(44) NOT NULL,
linkId CHAR(44) NOT NULL,
FOREIGN KEY (id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE dfd_ancestry
(
ancestorId CHAR(44) NOT NULL,
descendantId CHAR(44) NOT NULL,
depth INT NOT NULL,
FOREIGN KEY (ancestorId)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (descendantId)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE element_list
(
diagramId CHAR(44) NOT NULL,
elementId CHAR(44) NOT NULL,
FOREIGN KEY (elementId)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE dianode
(
childDiagramId CHAR(44),
diaNodeId CHAR(44) NOT NULL,
FOREIGN KEY (diaNodeId)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE hash
(
id CHAR(44) NOT NULL,
hash CHAR(255) NOT NULL,
PRIMARY KEY(id),
FOREIGN KEY (id)
REFERENCES users(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

#Grant proper privileges
CREATE USER 'dedc_user'@'localhost' IDENTIFIED BY 'dedc';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.entity TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.element TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.link TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.dianode TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.dfd_ancestry TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.users TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.hash TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, DELETE ON dedc.element_list TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, DELETE ON dedc.node TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, DELETE ON dedc.locks TO 'dedc_user'@'localhost';
GRANT DELETE ON dedc.entity TO 'dedc_user'@'localhost';

#Grant proper privileges - don't use these on anything but testing DB
CREATE USER 'tester'@'localhost' IDENTIFIED BY 'test';
GRANT ALL ON dedc.* TO 'tester'@'localhost';

#Insert a user
INSERT INTO users (id, userName, organization) VALUES ('RlyxJ3ZxPsdOr9rFTb9UrTrGKRBUQxWbclf9Gv0Fz1Mx', 'Geoff', 'InD');
INSERT INTO users (id, userName, organization) VALUES ('3LToOJ5L0gRLVRcYDThHYxne49Ai13xKHwVJx6Y4Ixox', 'Jeb', 'InD');
INSERT INTO users (id, userName, organization) VALUES ('4Q8RTb7YK93Zt7QhJx2LZlFPl8igZkYmauAhIh2djMAx', 'Eve', 'Evil Inc');

#password for Geoff is password1
INSERT INTO hash (id, hash) VALUES ('RlyxJ3ZxPsdOr9rFTb9UrTrGKRBUQxWbclf9Gv0Fz1Mx', '$2y$10$RMwCqfIrwxTmnzQoEw8B4ePQcH3TmVIQ35xQcTbltOdtXbPvT.3pi');

#password for Bob is password2
INSERT INTO hash (id, hash) VALUES ('3LToOJ5L0gRLVRcYDThHYxne49Ai13xKHwVJx6Y4Ixox', '$2y$10$VVK5rcQAz/wd59ckep16Quu8T/vJ7ExXRmW6Ya3lVdc2xnwQV6xUS');

#password for Eve is password3
INSERT INTO hash (id, hash) VALUES ('4Q8RTb7YK93Zt7QhJx2LZlFPl8igZkYmauAhIh2djMAx', '$2y$10$o5iTjkxknv6rcQXEX/7ZhOvG4abRfaQ488vxpavF1rXdIt0Hd/qrC');
