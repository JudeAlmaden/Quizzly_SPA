# QuizzlySpa - Interactive Quiz Management System

QuizzlySpa is a modern, real-time quiz application built with **Laravel**, **Vue.js**, and **Inertia.js**. It features a dynamic, vibrant UI with real-time gameplay, admin controls, and live rankings.

---

## 🚀 Features

### 🎮 Game Interface
- **Vibrant Theme**: Deep purple/violet gradients with animated background elements.
- **Glassmorphism UI**: Modern, translucent UI components with frosted glass effects.
- **Real-time Updates**: Powered by Laravel Reverb/Echo for instant question reveals, timer sync, and score updates.

### 🛠 Quiz Administration
- **Question Management**: Support for MCQ, True/False, and Identification types.
- **Game Control**: Choose categories, set custom timers, and reveal answers.
- **Live Rankings**: Real-time leaderboard tracking player scores and positions.

---

## 🛠 Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Vue.js 3 (Script Setup), Tailwind CSS
- **Full Stack**: Inertia.js
- **Real-time**: Laravel Reverb
- **Containerization**: Docker (Docker Compose)

---

## ⚡ Getting Started

### 1. Local Setup (Without Docker)

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```
2. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. **Setup Database**
   - Configure `DB_HOST=127.0.0.1` and other `DB_*` variables in `.env`.
   - Run `php artisan migrate`.

4. **Start Servers** (Multiple terminals)
   ```bash
   php artisan serve
   npm run dev
   php artisan reverb:start
   php artisan queue:listen
   ```

   4.1 LAN setup deployment
   ```bash
   # Change APP_URL to your local IP
   APP_URL=http://192.168.1.22:8000
   npm run builld
   php artisan reverb:start --host=0.0.0.0 --port=8080
   php artisan queue:listen
   php artisan serve --host=0.0.0.0
   
   ```


### 2. Docker Setup (Recommended)

1. **Setup Environment**
   ```bash
   cp .env.docker .env
   # Ensure DB_HOST=mysql in .env
   ```
2. **Build and Start Containers**
   ```bash
   docker compose up -d --build
   ```
3. **Initialize Application**
   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --force
   docker compose exec app php artisan db:seed --force
   ```
4. **Access the App**
   - **Main App**: [http://localhost](http://localhost) (using Nginx)
   - **Vite Dev**: Handled automatically on port 5173.

---

## ⚙️ Environment Configuration

### Key Variables to Change

| Variable | Local Value | Docker Value | Description |
| :--- | :--- | :--- | :--- |
| `DB_HOST` | `127.0.0.1` | `mysql` | The database server hostname. |
| `DB_PASSWORD` | `(your_pass)`| `secret` | Default password defined in `docker-compose.yml`. |
| `REDIS_HOST` | `127.0.0.1` | `redis` | The Redis server hostname. |
| `REVERB_HOST` | `0.0.0.0` | `0.0.0.0` | Listening interface for Reverb. |
| `VITE_REVERB_HOST`| `localhost` | `localhost` | Browser WebSocket connection address. |

> [!IMPORTANT]
> When running on a local network (e.g., testing on mobile), change `VITE_REVERB_HOST` and `APP_URL` to your machine's local IP (e.g., `192.168.1.XX`).

---

## 🛠 Running Commands in Docker

Since everything is containerized, run commands through `docker compose exec app`:

- **Run Migrations**: `docker compose exec app php artisan migrate`
- **Tinker**: `docker compose exec app php artisan tinker`
- **Clear Cache**: `docker compose exec app php artisan optimize:clear`
- **NPM (in vite container)**: `docker compose exec vite npm install <pkg>`

---

## 🔍 Troubleshooting

### 1. "Database is uninitialized" (MySQL Error)
If the MySQL container fails to start, ensure `DB_PASSWORD` is set in your `.env` file before running `docker compose up`. The `docker-compose.yml` uses this variable to initialize the database.

### 2. "Connection Refused" (Database)
Ensure `DB_HOST=mysql` is used when running inside Docker. `127.0.0.1` refers to the container itself, not the database container.

### 3. Vite / Node.js Version Error
The `vite` container uses **Node.js 20**. If you see version errors or Rollup architecture mismatches, ensure you are using the updated `docker-compose.yml` which handles `node_modules` isolation.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
