-- Execute in the dedc DB
BEGIN;

TRUNCATE TABLE entity CASCADE; 
TRUNCATE TABLE element; 
TRUNCATE TABLE dataflow; 
TRUNCATE TABLE external_links; 
TRUNCATE TABLE element_list; 
TRUNCATE TABLE multiprocess; 

COMMIT;