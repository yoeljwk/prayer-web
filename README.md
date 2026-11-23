# 🙏 Prayer Web

Prayer Web is a web application designed to help people share prayer requests, support one another, and build faith-based communities.

Built with **Laravel 11** as the backend and **Vue 3 + TypeScript** as the frontend.

## ✨ Features

- **Community Management** – Create, discover, and join public or private prayer communities.
- **Prayer Requests** – Share prayer requests with different categories and privacy settings.
- **Prayer Support** – Let others know you are praying for them.
- **Comments & Updates** – Share encouragement and updates on prayer requests.
- **Personal Dashboard** – View your prayer activity, communities, and requests.
- **Authentication** – Secure login and API authentication using Laravel Sanctum.

## 🛠️ Tech Stack

**Backend**
- Laravel 11
- PHP 8.2+
- Laravel Sanctum
- MySQL / SQLite / PostgreSQL

**Frontend**
- Vue 3
- TypeScript
- Vite
- Tailwind CSS
- Pinia
- Vue Router
- Lucide Icons

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2
- Composer >= 2.x
- Node.js >= 18
- npm

### 1. Backend Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Backend runs at: `http://127.0.0.1:8000`

### 2. Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

Frontend runs at: `http://localhost:5173`

## 📁 Project Structure

```text
prayer-web/
├── backend/       # Laravel REST API
├── frontend/      # Vue 3 Application
└── docs/          # Documentation
```

## 🤝 Contributing

Contributions are welcome! Feel free to fork the repository, create a feature branch, and submit a pull request.

## 📄 License

This project is licensed under the [MIT License](LICENSE).
