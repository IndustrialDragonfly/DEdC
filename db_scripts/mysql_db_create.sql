CREATE DATABASE dedc;
USE dedc;

CREATE TABLE entity
(
id CHAR(44) NOT NULL,
label VARCHAR(100) NOT NULL,
type SMALLINT NOT NULL, # Could be an enum, maps to types
originator VARCHAR(100), # Username
PRIMARY KEY (id)
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

CREATE TABLE dataflow 
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
df_id CHAR(44) NOT NULL,
FOREIGN KEY (id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (df_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE external_links
(
dfd_id CHAR(44) NOT NULL,
df_id CHAR(44) NOT NULL,
FOREIGN KEY (dfd_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (df_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE element_list
(
dfd_id CHAR(44) NOT NULL,
el_id CHAR(44) NOT NULL,
FOREIGN KEY (dfd_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (el_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

CREATE TABLE multiprocess
(
dfd_id CHAR(44) NOT NULL,
mp_id CHAR(44) NOT NULL,
FOREIGN KEY (dfd_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE,
FOREIGN KEY (mp_id)
REFERENCES entity(id)
ON DELETE CASCADE
ON UPDATE CASCADE
) Engine InnoDB;

#Grant proper privileges
CREATE USER 'dedc_user'@'localhost' IDENTIFIED BY 'dedc';
GRANT SELECT, INSERT, UPDATE ON dedc.* TO 'dedc_user'@'localhost';
GRANT DELETE ON dedc.entity TO 'dedc_user'@'localhost';

#Grant proper privileges - don't use these on anything but testing DB
CREATE USER 'tester'@'localhost' IDENTIFIED BY 'test';
GRANT ALL ON dedc.* TO 'tester'@'localhost';

