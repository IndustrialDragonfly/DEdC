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

CREATE TABLE entity
(
id CHAR(44) NOT NULL,
label VARCHAR(100) NOT NULL,
type VARCHAR(20) NOT NULL,
originator VARCHAR(100), # Username
PRIMARY KEY (id),
FOREIGN KEY (type)
REFERENCES types(type)
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
origin_id CHAR(44), 
dest_id CHAR(44), 
PRIMARY KEY (id), 
FOREIGN KEY (id) 
REFERENCES entity(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE node
(
id CHAR(44) NOT NULL,
link_id CHAR(44) NOT NULL,
FOREIGN KEY (id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE dfd_ancestry
(
ancestor_id CHAR(44) NOT NULL,
descendant_id CHAR(44) NOT NULL,
depth INT NOT NULL,
FOREIGN KEY (ancestor_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (descendant_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE element_list
(
dfd_id CHAR(44) NOT NULL,
el_id CHAR(44) NOT NULL,
FOREIGN KEY (el_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE subdfdnode
(
dfd_id CHAR(44),
subdfdnode_id CHAR(44) NOT NULL,
FOREIGN KEY (subdfdnode_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

#Grant proper privileges
CREATE USER 'dedc_user'@'localhost' IDENTIFIED BY 'dedc';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.entity TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.element TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.link TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.subdfdnode TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON dedc.dfd_ancestry TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, DELETE ON dedc.element_list TO 'dedc_user'@'localhost';
GRANT SELECT, INSERT, DELETE ON dedc.node TO 'dedc_user'@'localhost';
GRANT DELETE ON dedc.entity TO 'dedc_user'@'localhost';

#Grant proper privileges - don't use these on anything but testing DB
CREATE USER 'tester'@'localhost' IDENTIFIED BY 'test';
GRANT ALL ON dedc.* TO 'tester'@'localhost';
