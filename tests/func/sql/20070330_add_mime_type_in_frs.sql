-- Add a new colum in the FRS to save the mime type of the uploaded file.
-- This mime type will be reuse when sending the document.
-- Alain Peyrat 2007-03-30
ALTER TABLE frs_file ADD COLUMN mime_type character varying(30);
UPDATE frs_file SET mime_type='application/octet-stream' WHERE mime_type IS NULL;
ALTER TABLE frs_file ALTER COLUMN mime_type SET DEFAULT 'application/octet-stream'::character varying;
ALTER TABLE frs_file ALTER COLUMN mime_type SET NOT NULL;

---
--- Re-create the frs_view to add the mime_type field.
---
DROP VIEW frs_file_vw;
CREATE VIEW frs_file_vw AS
  SELECT frs_file.file_id, frs_file.filename, frs_file.release_id, frs_file.type_id, frs_file.processor_id, frs_file.release_time, frs_file.file_size, frs_file.post_date, frs_file.mime_type, frs_filetype.name AS filetype, frs_processor.name AS processor, frs_dlstats_filetotal_agg.downloads
   FROM frs_filetype, frs_processor, frs_file
   LEFT JOIN frs_dlstats_filetotal_agg ON frs_dlstats_filetotal_agg.file_id = frs_file.file_id
  WHERE frs_filetype.type_id = frs_file.type_id AND frs_processor.processor_id = frs_file.processor_id;
