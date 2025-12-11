# Database Directory

This directory is used to store the SQLite database file (shares.db).

## Important Notes

- This directory will be created automatically on first use
- Ensure the web server has write permissions to this directory
- DO NOT delete the database file (shares.db) during updates
- The database contains all share information and file metadata

## Security

The .htaccess file in this directory denies all direct access to database files.
