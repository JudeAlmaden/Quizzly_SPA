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

### 2. Setup (Recommended)

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
   - Configure Your `DB_*` variables in `.env`.
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

   # Change VITE_REVERB_HOST to your local IP
   VITE_REVERB_HOST=192.168.1.22

   npm run build
   php artisan reverb:start --host=0.0.0.0 --port=8080
   php artisan queue:listen
   php artisan serve --host=0.0.0.0
   ```

---

## ⚙️ Environment Configuration

### Key Variables to Change

| Variable | Local Value | Description |
| :--- | :--- | :--- |
| `DB_HOST` | `127.0.0.1` | The database server hostname. |
| `DB_PASSWORD` | `(your_pass)`| Your database password. |
| `REDIS_HOST` | `127.0.0.1` | The Redis server hostname. |
| `REVERB_HOST` | `0.0.0.0` | Listening interface for Reverb. |
| `VITE_REVERB_HOST`| `localhost` | Browser WebSocket connection address. |

> [!IMPORTANT]
> When running on a local network (e.g., testing on mobile), change `VITE_REVERB_HOST` and `APP_URL` to your machine's local IP (e.g., `192.168.1.XX`).

---

## 🔍 Troubleshooting

### 1. "Connection Refused" (Database)
Ensure your database server is running and the credentials in `.env` are correct.

### 2. Reverb Connection Issues
If you cannot connect to the WebSocket server, ensure `php artisan reverb:start` is running and the `VITE_REVERB_HOST` matches the IP you are using to access the site.

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
