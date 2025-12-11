# Storage Directory

This directory is used to store:
- Uploaded files (in subdirectories named by unique tokens)
- Session files
- SQLite database

## Important Notes

- This directory will be created automatically on first use
- Ensure the web server has write permissions to this directory
- DO NOT delete this directory or its contents during updates
- All uploaded files are protected from direct URL access by .htaccess

## Security

The .htaccess file in this directory denies all direct access to files.
Downloads are only possible through the PHP application.
