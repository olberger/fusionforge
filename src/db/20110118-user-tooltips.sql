ALTER TABLE USERS ADD COLUMN tooltips INT;
ALTER TABLE USERS ALTER COLUMN tooltips SET DEFAULT 1;
UPDATE USERS set tooltips = 1;
