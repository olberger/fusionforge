ALTER TABLE GROUPS ADD COLUMN use_docman_search INT;
ALTER TABLE GROUPS ALTER COLUMN use_docman_search SET DEFAULT 1;
UPDATE GROUPS SET use_docman_search = 1;
ALTER TABLE GROUPS ADD COLUMN force_docman_reindex INT;
ALTER TABLE GROUPS ALTER COLUMN force_docman_reindex SET DEFAULT 0;
UPDATE GROUPS SET force_docman_reindex = 0;
ALTER TABLE DOC_GROUPS ADD COLUMN stateid INT;
ALTER TABLE DOC_GROUPS ADD COLUMN stateid SET DEFAULT 1;
UPDATE DOC_GROUPS SET stateid = 1;
