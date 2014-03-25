USE dedc;

BEGIN;

SET foreign_key_checks=0;
TRUNCATE TABLE entity; 
TRUNCATE TABLE element; 
TRUNCATE TABLE link; 
TRUNCATE TABLE element_list; 
TRUNCATE TABLE dianode;
TRUNCATE TABLE dfd_ancestry;
TRUNCATE TABLE node;
SET foreign_key_checks=1;

COMMIT;
