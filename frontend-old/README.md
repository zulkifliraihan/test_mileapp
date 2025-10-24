# Frontend (Vue 3 + TypeScript + Vite + Tailwind v4)

Clean, responsive UI using Tailwind CSS with a simple dashboard and tasks CRUD.

## Commands
- `pnpm i` – install dependencies
- `pnpm dev` – start dev server
- `pnpm build` – type-check and build

## Environment
- Copy `.env.example` to `.env` and set `VITE_APP_API_URL` (e.g. `http://localhost:8000`).
- When `VITE_APP_API_URL` is unset, login and tasks use mock data.

## Routing
- `/login` – public login (mock token auth)
- `/dashboard/tasks` – protected tasks module (CRUD)

## Tech
- Vue 3 (Composition API), Vue Router, Pinia
- Tailwind CSS v4 via `@tailwindcss/vite`
- Vite + TypeScript
