# TrioTrek – BCA Project Management System

TrioTrek is a web-based **Project Management System** developed as part of a **BCA Final Year Project**. It provides a collaborative platform for **students, teachers, and administrators** to manage academic projects efficiently.

---

## 📌 Project Overview

The system is designed to simplify academic project workflows by centralizing:

- user access and role-based dashboards,
- project idea submission and tracking,
- group collaboration,
- announcements and communication,
- feedback and document sharing.

This repository is primarily implemented in **PHP**, with supporting **HTML** and **CSS** for UI.

---

## ✨ Core Features

- 🔐 User authentication
  - Login
  - Registration
  - Forgot/Reset password
- 👥 Role-based dashboards for:
  - Admin
  - Teacher
  - Student
- 💡 Project idea submission and management
- 👨‍👩‍👧 Group creation and member handling
- 📢 Announcements and notifications
- 💬 Comments and feedback on projects
- 📁 File uploads (reports, assignments, documents)
- 📧 Email integration using **PHPMailer**

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL
- **Mail Service:** PHPMailer
- **Version Control:** Git & GitHub

---

## 📊 Language Composition

Based on repository analysis:

- **PHP:** 93.7%
- **HTML:** 5.0%
- **CSS:** 1.3%

---

## 📂 Project Structure

```text
TRIOTREK/
├── admin/           # Admin-only dashboards and management pages
├── user/            # Student/teacher/user-facing dashboards and flows
├── includes/        # Shared PHP includes (DB connection)
├── assets/          # Shared static assets (CSS/images)
├── uploads/         # Uploaded documents/files (kept as-is)
├── login.php        # Main login entry point
├── register.php     # Registration entry point
├── forgot_password.php / reset_password.php
└── README.md
```

### Entry points

- Public landing page: `index.html`
- Authentication: `login.php`
- Admin dashboard: `admin/admin_dashboard.php`
- Teacher dashboard: `user/teacher_dashboard.php`
- Student dashboard: `user/user_dashboard.php`

### Migration compatibility

- Legacy top-level PHP routes are kept as lightweight compatibility stubs that load files from `admin/` or `user/`.
- Shared DB include moved from `db.php` to `includes/db.php`.

---

## 🚀 Getting Started

### Prerequisites

Make sure the following are installed:

- XAMPP / WAMP / MAMP (or any PHP + MySQL local stack)
- PHP (latest stable recommended)
- MySQL
- Git (optional)

### Installation

1. **Clone the repository**

```bash
git clone https://github.com/JayPatel171143/TRIOTREK.git
```

2. **Move project to server root**

- For XAMPP: place inside `htdocs`
- Example path: `C:/xampp/htdocs/TRIOTREK`

3. **Create database**

- Open phpMyAdmin
- Create a new MySQL database
- Import SQL file (if available)

4. **Configure database connection**

- Open `includes/db.php`
- Update database host, username, password, and DB name

5. **Start services**

- Start **Apache** and **MySQL** from your local server control panel

6. **Run in browser**

```text
http://localhost/TRIOTREK/TrioTrek
```

(Adjust URL based on your folder structure.)

---

## ▶️ Usage Flow

1. Open the application in browser.
2. Register or log in with your account.
3. Access dashboard according to your role (Admin/Teacher/Student).
4. Create/manage project groups and submissions.
5. Upload required documents and review feedback.

---

## 🔒 Roles and Access

- **Admin:** System-level control, user/project oversight
- **Teacher:** Review projects, provide feedback, post updates
- **Student:** Submit ideas, join/manage groups, upload work

---

## 📸 Screenshots (Recommended)

Add screenshots for better documentation:

- Login/Register page
- Admin dashboard
- Teacher dashboard
- Student dashboard
- Project submission page

---

## 🧪 Future Enhancements

- Better UI/UX and responsive design improvements
- Role permission hardening and security enhancements
- Advanced project progress tracking
- Search/filtering for projects and members
- Exportable reports and analytics
- Unit/integration testing support

---

## 🤝 Contributing

This is currently an academic project. If contributions are enabled later:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Open a pull request

---

## 📄 License

No license has been added yet.

If you plan to open-source this project, consider adding an MIT License.

---

## 👨‍🎓 Academic Note

This project is maintained for **learning, demonstration, and academic purposes** as part of a BCA final year submission.

---

## 📬 Contact

**Maintainer:** Jay Patel  
**GitHub:** [@JayPatel171143](https://github.com/JayPatel171143)
