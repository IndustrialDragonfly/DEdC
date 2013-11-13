USE dedc;

BEGIN;
SET foreign_key_checks=0;
TRUNCATE TABLE entity; 
TRUNCATE TABLE element; 
TRUNCATE TABLE dataflow; 
TRUNCATE TABLE external_links; 
TRUNCATE TABLE element_list; 
TRUNCATE TABLE multiprocess; 

SET foreign_key_checks=1;

COMMIT;