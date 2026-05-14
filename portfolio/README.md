# Full-Stack Portfolio Website

## Setup
1. Create database: `CREATE DATABASE portfolio_db CHARACTER SET utf8mb4;`
2. Import `database.sql` into MySQL.
3. Update DB credentials in `config.php`.
4. Ensure Apache points to the `/portfolio/` directory.
5. Visit `/portfolio/admin/login.php`.

Default admin credentials:
- Username: `admin`
- Password: `admin123`

## Notes
- Upload folders: `/uploads/profile`, `/uploads/projects`, `/uploads/cv`
- Contact form posts to `/portfolio/api/contact.php`.
