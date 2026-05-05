# AssignMate

AssignMate is a full-stack study task management app that helps users organize academic work in one place. It supports registration, login, task CRUD, search and filters, dashboard analytics, and a dark mode UI.

## Features

- User registration and login
- Session-based authentication
- Create, edit, update, and delete tasks
- Filter tasks by status, subject, and search text
- Dashboard stats with completion progress
- Responsive UI with light and dark mode
- Secure backend queries with prepared statements

## Tech Stack

- `Vue 3` - frontend UI framework
- `Vue Router` - client-side routing and protected pages
- `Vite` - frontend dev server and build tool
- `Axios` - API communication from the frontend
- `PHP` - REST API backend
- `PDO` - secure MySQL database access
- `MySQL` - persistent task and user storage
- `XAMPP / Apache` - local server environment for PHP and MySQL

## Project Structure

```text
study_planner/
  api/        PHP REST API
  frontend/   Vue 3 app
  database.sql
```

## Screenshots

Add screenshots here if you want to showcase the UI on GitHub.

## Requirements

- XAMPP with Apache and MySQL
- Node.js 18+
- npm

## Setup

### 1) Clone the repository

```powershell
git clone <your-repo-url>
cd study_planner
```

### 2) Create the database

1. Open `http://localhost/phpmyadmin`
2. Create a database named `study_planner`
3. Import `database.sql`

### 3) Configure the backend

Create `api/.env` from the example file:

```powershell
cd C:\xampp\htdocs\study_planner\api
Copy-Item .env.example .env
```

Example backend config:

```env
DB_HOST=127.0.0.1
DB_PORT=3307
DB_NAME=study_planner
DB_USER=root
DB_PASS=
```

If your MySQL port is different, update `DB_PORT` accordingly.

### 4) Configure the frontend

```powershell
cd C:\xampp\htdocs\study_planner\frontend
Copy-Item .env.example .env
npm install
```

Set the frontend API URL in `frontend/.env`:

```env
VITE_API_BASE_URL=http://localhost/study_planner/api
```

## Run the App

### Backend

1. Start Apache and MySQL in XAMPP
2. Make sure the backend is available at:

```text
http://localhost/study_planner/api
```

### Frontend

```powershell
cd C:\xampp\htdocs\study_planner\frontend
npm run dev
```

Open the app at:

```text
http://localhost:5173
```

## API Endpoints

- `POST /register`
- `POST /login`
- `POST /logout`
- `GET /me`
- `GET /tasks`
- `POST /tasks`
- `PUT /tasks/{id}`
- `DELETE /tasks/{id}`

## How It Works

- Vue 3 renders the UI and handles client-side navigation.
- Vue Router protects dashboard routes and redirects users based on auth state.
- Axios sends requests to the PHP REST API.
- PHP handles authentication, validation, and task logic.
- MySQL stores users and tasks.
- PHP sessions keep users logged in across refreshes.

## Environment Notes

- The app uses browser `localStorage` to remember the theme preference.
- CORS is configured so the frontend on `5173` can talk to the backend under Apache.
- If you run into a database error, confirm MySQL is running and the port in `api/.env` matches XAMPP.

## Future Improvements

- Add task categories or tags
- Add calendar/reminder support
- Add drag-and-drop task organization
- Add charts for weekly productivity trends

## License

Add your preferred license here if you plan to publish the project publicly.
