# 🙏 Prayer Web

A modern full-stack web application designed to foster spiritual connection, community prayer support, and collaborative faith journeys. Built with a robust **Laravel 11** REST API backend and a responsive **Vue 3 + TypeScript** frontend.

---

## 🌟 Key Features

- **👥 Community & Group Management**
  - Create and manage prayer communities (Public or Invite-Only).
  - Secure join-by-code system for private prayer circles.
  - Member role management and community discovery directory.

- **🙏 Prayer Requests & Intercession**
  - Share prayer requests with detailed categories and privacy levels.
  - Interactive prayer support system (let others know you are praying for them).
  - Comment threads for words of encouragement and testimony updates.

- **📊 Personal Dashboard**
  - Overview of active prayer requests, joined communities, and recent prayer activity.
  - Quick action shortcuts to submit prayers or connect with prayer partners.

- **🔐 Authentication & Security**
  - Token-based API authentication powered by Laravel Sanctum.
  - Form validation, authorization policies, and route protection.

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11 (PHP 8.2+)
- **Authentication:** Laravel Sanctum
- **Database ORM:** Eloquent ORM (MySQL / SQLite / PostgreSQL compatible)
- **API Architecture:** RESTful JSON API

### Frontend
- **Framework:** Vue 3 (Composition API, `<script setup>`)
- **Language:** TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **State Management:** Pinia
- **Routing:** Vue Router
- **Icons:** Lucide Icons

---

## 🚀 Getting Started

### Prerequisites
Make sure you have the following installed on your machine:
- [PHP](https://www.php.net/) >= 8.2
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) >= 18.x and `npm`

---

### 1. Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Configure environment file
cp .env.example .env

# Generate application encryption key
php artisan key:generate

# Run database migrations
php artisan migrate

# Start Laravel development server
php artisan serve
```
The backend API will run on `http://127.0.0.1:8000`.

---

### 2. Frontend Setup

```bash
# Navigate to frontend directory
cd frontend

# Install Node dependencies
npm install

# Start Vite development server
npm run dev
```
The frontend application will be accessible at `http://localhost:5173`.

---

## 📁 Project Structure

```text
prayer-web/
├── backend/                  # Laravel 11 Backend Application
│   ├── app/
│   │   ├── Http/Controllers/ # REST API Controllers
│   │   ├── Models/           # Eloquent Models (User, PrayerRequest, etc.)
│   │   └── Requests/         # Form Request Validation Rules
│   ├── database/migrations/  # Database Schemas and Migrations
│   └── routes/api.php        # API Route Definitions
├── frontend/                 # Vue 3 Frontend Application
│   ├── src/
│   │   ├── components/       # Reusable UI & Feature Components
│   │   ├── composables/      # Shared Composition Logic
│   │   ├── views/            # Page Views (Dashboard, Community, etc.)
│   │   └── router/           # Client-side Route Definitions
└── docs/                     # Architecture & Planning Documentation
```

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).
