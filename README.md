# Mileapp Tasks — Full‑Stack Demo

A full‑stack example featuring a Laravel 12 backend with MongoDB and a modern Vue 3 frontend (TypeScript, Vite, Tailwind CSS). The app demonstrates token‑based authentication and a Task module with protected CRUD, filters, pagination, and sorting.

## Tech Stack

- Backend: Laravel 12 (PHP 8.4), JWT Auth, MongoDB
- Database: MongoDB 6
- Frontend: Vue 3, TypeScript, Vite, Tailwind CSS
- Runtime: Docker + Docker Compose

## Features

- Authentication
  - Login (JWT token issuance)
  - Logout (token invalidation)
- Tasks
  - List with server‑side pagination and filters (Title, Status)
  - Client‑side due date filter (per page)
  - Sorting (title, status, due_date, created_at)
  - Create/Update/Delete via modals with confirmation
  - Protected routes: only authenticated users can access and modify tasks
- Clean, responsive UI with Tailwind CSS

## Repository Structure

- `backend/` — Laravel 12 API with MongoDB
- `frontend/` — Vue 3 (TS, Vite, Tailwind) SPA
- `docker-compose.yml` — Services for frontend (Nginx), backend (PHP CLI), and MongoDB

## Quick Start

Requirements:
- Docker and Docker Compose

Run all services:

```bash
docker compose up -d
```

This will:
- Start MongoDB (exposed on host port 27018)
- Seed the database (admin user + sample tasks)
- Start the Laravel API at http://localhost:8000
- Build and serve the production frontend at http://localhost:8080

Default credentials (seeded):
- Email: `admin@mileapp.com`
- Password: `12345678`

## Service Details

- Backend
  - URL: `http://localhost:8000`
  - Seeds run automatically on container start (DatabaseSeeder -> UserSeeder, TaskSeeder)
  - Uses Docker network DSN for MongoDB: `mongodb://root:example@mongodb:27017/mileapp?authSource=admin`

- Frontend
  - URL: `http://localhost:8080`
  - Built with Vite and served by Nginx in production mode
  - Targets the backend at `http://localhost:8000`

## Notes

- If you change the backend host/port, update the frontend build environment accordingly (VITE_API_BACKEND_URL) and rebuild the frontend image.
- For local development outside Docker, you can still run the frontend with Vite (`pnpm dev`) and the backend with `php artisan serve`, but Docker is the recommended path here.

## Architecture & Rationale

- Explain what was built: A JWT-secured Task module on Laravel 12 (MongoDB) with a Vue 3 SPA. The backend exposes REST endpoints for authentication and task CRUD with server-side filtering, sorting, and pagination. The frontend provides a responsive UI with modal-based create/update and guarded routes.
- Design decisions: Chose MongoDB for flexible document modeling of tasks; repository/service layers to keep controllers thin and testable; JWT for stateless auth suitable for SPA; explicit resource transformers for consistent API responses; Docker for reproducible environments.
- Strengths: Clean layering (Controller → Service → Repository → Model), predictable pagination contract, defensive request validation, and startup automation (healthcheck-gated seeding and index creation).

### Database Indexes: What and Why

Indexes are created at container init via `db/indexes.js` to match the most common query patterns used by the API:
- `tasks`
  - `{ status: 1 }`: accelerates filtering by status in list views.
  - `{ created_at: -1 }`: matches default sort (newest first) without in-memory sorts.
  - `{ due_date: 1 }`: supports filtering/ordering by due date.
  - `{ title: 'text' }`: enables text search when filtering by title.
- `users`
  - `{ email: 1, unique: true }`: guarantees uniqueness for login/registration.
  - `{ created_at: -1 }`: supports admin/user listings ordered by recency.
  - `{ email_verified_at: -1 }`: speeds up queries that segment verified/unverified users.
  - `{ name: 'text', email: 'text' }`: basic text lookup for admin/user search.

Notes on choices: We prefer targeted single-field indexes to keep write amplification moderate and storage overhead low while matching our filters/sorts. If future traffic concentrates on combined filters (e.g., status + created_at), we can add a compound index like `{ status: 1, created_at: -1 }` guided by profiler metrics.
