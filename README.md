# To-Do List (PHP + MySQL)

A simple to-do list web application built with plain PHP and MySQL, designed to run on XAMPP. Users can sign up, log in, and manage their own personal to-do items — add, edit, delete, and mark them as done — plus customize their profile avatar.

## Features

- **User authentication** — sign up with name, email, and hashed password (`password_hash` / `password_verify`); log in and log out with sessions.
- **Personal to-do list** — each user sees only their own todos (scoped by `user_id`).
- **Add todos** — quickly add a new task from the top bar.
- **Edit / delete todos** — with confirmation dialogs.
- **Toggle status** — check a todo off as done (or undo it).
- **Profile avatar** — upload an image to replace your avatar.
- **Prepared statements** — all database queries use `mysqli` prepared statements to prevent SQL injection; output is escaped with `htmlspecialchars`.

## Tech Stack

| Layer    | Technology              |
|----------|-------------------------|
| Backend  | PHP (procedural, MySQLi) |
| Database | MySQL (XAMPP)           |
| Frontend | HTML, CSS, vanilla JS    |
| Server   | Apache (XAMPP)          |

## Requirements

- [XAMPP](https://www.apachefriends.org/) (or any Apache + PHP + MySQL stack)
- PHP 7.4+ (uses `password_hash`, prepared statements)

## Installation

1. **Clone or copy the project** into your web root:

   ```
   C:\xampp\htdocs\toDoList-php-mysql
   ```

2. **Start Apache and MySQL** from the XAMPP Control Panel.

3. **Create the database** — import the included schema:

   ```
   C:\xampp\mysql\bin\mysql.exe -u root < C:\xampp\htdocs\toDoList-php-mysql\setup.sql
   ```

   Or via phpMyAdmin: open `http://localhost/phpmyadmin`, go to **Import**, and select `setup.sql`.

4. **Open the app** in your browser:

   ```
   http://localhost/toDoList-php-mysql/
   ```

## Configuration

Database connection settings live in `db.php`:

```php
$conn = mysqli_connect("localhost", "root", "", "todolist");
```

- Host: `localhost`
- User: `root`
- Password: *(empty — default XAMPP)*
- Database: `todolist`

## Project Structure

```
toDoList-php-mysql/
├── index.php        # Login page (entry point)
├── signup.php       # Registration page
├── home.php         # To-do list dashboard (add/edit/delete/status/avatar)
├── db.php           # MySQL connection
├── setup.sql        # Database & table schema
├── admin.txt        # Reference credentials
├── style/
│   ├── login-signin.css   # Styles for login/signup
│   ├── home.css           # Styles for dashboard
│   └── style.css
└── img/             # Uploaded avatars & assets
```

## Database Schema

```sql
users (
  user_id   INT PK AUTO_INCREMENT,
  name      VARCHAR(100),
  email     VARCHAR(100) UNIQUE,
  password  VARCHAR(255),        -- bcrypt hash
  image     VARCHAR(255) NULL    -- avatar filename
)

todos (
  todo_id   INT PK AUTO_INCREMENT,
  title     VARCHAR(255),
  status    TINYINT DEFAULT 0,   -- 0 = pending, 1 = done
  user_id   INT FK → users(user_id)
)
```

## Usage

1. Open the app and click **Create a new account** to register.
2. Log in with your email and password.
3. Add todos from the input bar at the top.
4. Use the checkboxes to mark items done; **Edit** / **Delete** buttons manage them.
5. Click **Edit** on your avatar to upload a profile picture.
6. Click **Logout** to end your session.
