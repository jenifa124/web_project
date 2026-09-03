# Hospital Management System (PHP MVC)

Complete Hospital Management System built with pure PHP (procedural), MySQL, HTML/CSS/JS following strict MVC structure.

## Features Overview

### 4 User Roles

| Role | Unique Features (non-overlapping) |
|------|-----------------------------------|
| **Admin** | 1. Full User Management (CRUD)  2. Revenue Reports  3. Notices + Activity Logs |
| **Doctor** | 1. Availability Schedule  2. Write Prescriptions  3. Medical History Records |
| **Patient** | 1. Browse Doctors & Book Appointments  2. View Own Prescriptions  3. Pay Invoices Online |
| **Receptionist** | 1. Live Queue Management  2. Generate Invoices  3. Register Patients + Manage Appointments |

All roles support **Create, Read, Update, Delete, and Search** on their respective modules.

## Requirements

- XAMPP (Apache + MySQL + PHP 7.4+)
- Modern browser

## Installation (XAMPP)

1. Copy the `hospital_management` folder into `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux).

2. Start Apache and MySQL from XAMPP Control Panel.

3. Open phpMyAdmin → Import `database.sql`  
   (or run in MySQL CLI: `source /path/to/database.sql`)

4. Open browser:  
   `http://localhost/hospital_management/`

5. Login with demo accounts (password for all: **`password`**)

| Username    | Role          |
|-------------|---------------|
| admin       | Administrator |
| dr.smith    | Doctor        |
| reception   | Receptionist  |
| patient1    | Patient       |

## Security Implemented

- Session-based authentication
- Role-based access control (`require_role`)
- CSRF tokens on all forms
- Password hashing (`password_hash` / `password_verify`)
- Input sanitization (`htmlspecialchars`, `mysqli_real_escape_string`)
- Prepared-style escaping (procedural)
- XSS protection on output (`e()` helper)
- Activity logging of important actions

## Tech Stack Checklist

- [x] MVC Structure
- [x] MySQL Procedural (mysqli)
- [x] Session / Cookie Auth
- [x] PHP Validation
- [x] JS Client-side Validation
- [x] Ajax / JSON endpoints
- [x] Basic Web Security (CSRF, XSS, hashing)
- [x] Modern Responsive UI (HTML/CSS)
- [x] Feature Completeness (CRUD + Search for all roles)

## Project Structure

```
hospital_management/
├── index.php                 # Front Controller / Router
├── config/database.php
├── controllers/              # Auth, Admin, Doctor, Patient, Receptionist, Ajax
├── models/                   # All data access (procedural functions)
├── views/                    # Role-based views + partials
├── assets/css/style.css
├── assets/js/app.js
├── helpers/helpers.php
└── database.sql
```

## Notes

- Default password for sample users is `password`.
- Change the admin password after first login.
- Database credentials are in `config/database.php` (default XAMPP: root / empty password).
