-- On Postgres DB
-- first line must be run separately in phpgadmin
CREATE DATABASE dedc;
--Create and grant proper privileges
CREATE ROLE dedc_user WITH LOGIN PASSWORD 'dedc';
GRANT ALL PRIVILEGES ON DATABASE dedc TO dedc_user;
-- Tester role is ONLY to be created on test systems never in production DBs
CREATE ROLE tester WITH LOGIN PASSWORD 'test';
GRANT ALL PRIVILEGES ON DATABASE dedc TO tester;

-- Execute in dedc DB
CREATE TABLE types
(
type_id SMALLINT NOT NULL,
type_name VARCHAR(20) NOT NULL,
PRIMARY KEY (type_id)
);

-- Insert valid types into types table
INSERT INTO types(type_name, type_id) VALUES('Process', 0);
INSERT INTO types(type_name, type_id) VALUES('DataStore', 1);
INSERT INTO types(type_name, type_id) VALUES('Multiprocess', 2);
INSERT INTO types(type_name, type_id) VALUES('ExternalInteractor', 3);
INSERT INTO types(type_name, type_id) VALUES('DataFlow', 4);
INSERT INTO types(type_name, type_id) VALUES('DataFlowDiagram', 5);

CREATE TABLE entity
(
id CHAR(44) NOT NULL,
label VARCHAR(100) NOT NULL,
type SMALLINT NOT NULL,
originator VARCHAR(100), -- Username
PRIMARY KEY (id),
FOREIGN KEY (type)
REFERENCES types(type_id)
);

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
);

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
);

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
);

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
);

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
);

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
);

-- Grant privileges for normal user
GRANT SELECT, INSERT, UPDATE ON entity TO dedc_user;
GRANT DELETE ON entity TO dedc_user;
GRANT SELECT, INSERT, UPDATE ON dataflow TO dedc_user;
GRANT SELECT, INSERT, UPDATE ON element TO dedc_user;
GRANT SELECT, INSERT, UPDATE ON multiprocess TO dedc_user;
GRANT SELECT, INSERT, DELETE ON element_list TO dedc_user;
GRANT SELECT, INSERT, DELETE ON external_links TO dedc_user;
GRANT SELECT, INSERT, DELETE ON node TO dedc_user;

--Grant privileges for tester don't use these on anything but testing DB
GRANT ALL ON entity TO tester;
GRANT ALL ON dataflow TO tester;
GRANT ALL ON element TO tester;
GRANT ALL ON element_list TO tester;
GRANT ALL ON external_links TO tester;
GRANT ALL ON multiprocess TO tester;
GRANT ALL ON node TO tester;
