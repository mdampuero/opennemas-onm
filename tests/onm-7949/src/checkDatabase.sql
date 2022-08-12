SET @database=2;
SET @fk = (
  SELECT count(*)
  FROM information_schema.REFERENTIAL_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @database
  AND REFERENCED_TABLE_NAME = 'contents'
  AND CONSTRAINT_NAME = 'cover_id_pk_content'
);
SET @cover_id = (
  SELECT count(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @database
  AND TABLE_NAME = 'category'
  AND COLUMN_NAME = 'cover_id'
);
SHOW KEYS FROM category WHERE Key_name='cover_id';
SELECT IF (@cover_id + @fk = 2, 'TRUE', 'FALSE') as result;
