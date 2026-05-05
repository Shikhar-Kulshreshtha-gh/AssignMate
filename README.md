# Study Planner (Vue + PHP + MySQL)

## Project Structure

- `api/` -> PHP REST backend (XAMPP Apache compatible)
- `frontend/` -> Vue 3 frontend (Vite)
- `database.sql` -> MySQL schema

## Run with XAMPP (Recommended)

### 1) Start services

In XAMPP Control Panel, start:

- `Apache`
- `MySQL`

### 2) Setup database

1. Open `http://localhost/phpmyadmin`
2. Create a database named `study_planner`
3. Import [database.sql](/C:/xampp/htdocs/study_planner/database.sql)

### 3) Configure backend

In `C:\xampp\htdocs\study_planner\api`, create `.env` from `.env.example`:

```powershell
cd C:\xampp\htdocs\study_planner\api
Copy-Item .env.example .env
```

Default values:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=study_planner
DB_USER=root
DB_PASS=
```

If your XAMPP MySQL uses a custom port (for example `3307`), update `DB_PORT` in `api/.env` to that port.

Backend base URL:

- `http://localhost/study_planner/api`

### 4) Configure frontend

```powershell
cd C:\xampp\htdocs\study_planner\frontend
Copy-Item .env.example .env
npm install
npm run dev
```

Open frontend:

- `http://localhost:5173`

## API Endpoints

- `POST /register`
- `POST /login`
- `POST /logout`
- `GET /me`
- `GET /tasks`
- `POST /tasks`
- `PUT /tasks/{id}`
- `DELETE /tasks/{id}`

When base URL is `http://localhost/study_planner/api`, full register URL is:
`http://localhost/study_planner/api/register`.
