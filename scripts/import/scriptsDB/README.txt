It's one script to refactorize id's and generate slugs in DB for opennemas
This script add new usergroup Master and change some users


HOW USE:

1. You config database name and settings in db-config.inc.php
2. Execute ./refactorize.php
3. Is generated refactor_ids table in database with old and new id
4. If you want clear all values that therearen't in contents table.
   execute ./clear.php