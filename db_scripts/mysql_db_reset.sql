USE dedc;

BEGIN;

SET foreign_key_checks=0;
TRUNCATE TABLE entity; 
TRUNCATE TABLE element; 
TRUNCATE TABLE links; 
TRUNCATE TABLE element_list; 
TRUNCATE TABLE subdfdnode;
TRUNCATE TABLE dfd_ancestry;
SET foreign_key_checks=1;

COMMIT;
