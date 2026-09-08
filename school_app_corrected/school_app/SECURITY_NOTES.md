# School Management System - Pre-Publication Notes

This version contains the security/cleanup pass performed before publication.

## Included changes
- Protected application pages with the admin authentication guard.
- Added CSRF token generation/verification for state-changing forms.
- Changed student, grade and teacher deletion to POST requests with CSRF protection.
- Removed duplicate administrator profile update logic and made it use the logged-in admin ID.
- Made password changes use the logged-in admin ID and password hashing/verification.
- Hardened PHP session cookies (HttpOnly, SameSite=Lax, Secure when HTTPS is active).
- Added optional `config/database.local.php` support and `config/database.local.php.example`.
- Added UTF-8 (`utf8mb4`) database connection charset.
- Removed the missing `grades/grades.css` reference.
- Removed redundant theme systems from `js/app.js`, keeping the sun/moon toggle.
- Removed the debug console message.
- Converted the legacy `students/create.php` route to redirect to the current `students/add.php` page.
- Removed duplicate admin name rendering from the sidebar.
- Added `.zip`/backup exclusions to `.gitignore`.

## Important before making the GitHub repository public
1. Do not publish real student/teacher data or database dumps.
2. Keep `config/database.local.php` out of Git (it is ignored).
3. Use real production database credentials only in the hosting environment/local ignored config.
4. Enable HTTPS in production.
5. Test login, logout, all CRUD actions, grades, ranking, bulletin and settings after copying these files into the local XAMPP project.
6. This archive intentionally does not include the `.git` directory.
