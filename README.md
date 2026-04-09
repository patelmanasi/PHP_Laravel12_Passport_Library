# PHP_Laravel12_Passport_Library 
<<<<<<< HEAD
=======

## Introduction

PHP_Laravel12_Passport_Library is a learning-oriented Laravel project that demonstrates how to integrate Laravel Passport into a normal web-based Laravel 12 application.

Unlike most Passport examples that focus only on API authentication, this project uses Blade views, web routes, and session-based login to explain how Passport works internally within a standard Laravel web application.

The goal of this project is to help developers understand:

- How Laravel Passport is installed and configured
- What OAuth tables Passport creates and why they are needed
- How Passport integrates with Laravel’s default authentication system
- How a web application can be prepared for future API or OAuth expansion

---

## Project Overview

This project is a traditional web authentication system built using Laravel 12 and Laravel Passport.

### Application Features

- User registration
- User login
- Protected dashboard
- Logout functionality

### Passport Usage

Laravel Passport is installed to:

- Generate OAuth client credentials
- Create authentication-related tables (oauth_clients, oauth_access_tokens, etc.)
- Demonstrate how Passport extends Laravel’s authentication ecosystem

---

## Key Characteristics

- Web-based authentication using Blade templates
- Laravel session authentication for login/logout
- Laravel Passport for OAuth infrastructure
- Clean project structure following Laravel 12 standards

---


##  Tech Stack

* Laravel 12
* Laravel Passport
* PHP 8.2+
* MySQL
* Blade Templates
* Web Routes

---

##  Project Name

```
PHP_Laravel12_Passport_Library
```

---

##  Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Passport_Library "12.*"
```

```bash
cd PHP_Laravel12_Passport_Library
```

---

##  Step 2: Environment Configuration

Edit `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_passport_library
DB_USERNAME=root
DB_PASSWORD=
```

Create database `laravel12_passport_library` using migration command

```bash
php artisan migrate
```

---

##  Step 3: Install Laravel Passport

```bash
composer require laravel/passport
```

We only install Passport and use **basic features**, not advanced OAuth flows.

---

##  Step 4: Run Migrations

```bash
php artisan migrate
```

This creates Passport tables internally (OAuth2 infrastructure).

---

##  Step 5: Install Passport Keys

```bash
php artisan passport:install
```

This generates:

* Encryption keys
* OAuth clients (used internally)

---

##  Step 6: Migration Table (Users Default Table)

File: database/migrations/2014_10_12_000000_create_users_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

##  Step 7: User Model Update

```
app/Models/User.php
```

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

Passport tokens are attached to the user behind the scenes.

---

##  Step 8: Create Web Authentication Controller

```bash
php artisan make:controller Web/AuthController
```

```
app/Http/Controllers/Web/AuthController.php
```

```php
<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return redirect('/login')->with('success', 'Registered successfully');
    }


    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            return redirect('/dashboard');
        }


        return back()->withErrors(['Invalid credentials']);
    }

    public function dashboard()
    {
        return view('dashboard');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
```

---

##  Step 9: Create Blade Views

### 9.1) Register View

```
resources/views/auth/register.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-6">
            <h2 class="text-2xl font-bold text-center text-indigo-600">Create Your Account</h2>

            <!-- Success / Error Messages -->
            @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="/register" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Enter your full name" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white text-lg font-semibold rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                    REGISTER
                </button>
            </form>

            <p class="text-center text-sm text-gray-600">
                Already have an account?
                <a href="/login" class="text-indigo-600 hover:underline font-medium">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
```

###  9.2) Login View

```
resources/views/auth/login.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-6">
            <h2 class="text-2xl font-bold text-center text-indigo-600">Welcome Back</h2>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="/login" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white text-lg font-semibold rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                    LOGIN
                </button>
            </form>

            <p class="text-center text-sm text-gray-600">
                Don’t have an account?
                <a href="/register" class="text-indigo-600 hover:underline font-medium">Create one</a>
            </p>
        </div>
    </div>
</body>
</html>
```

### 9.3) Dashboard

```
resources/views/dashboard.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-indigo-600">
                Laravel Passport App
            </h1>

            <a href="/logout"
               class="text-sm font-semibold text-red-600 hover:text-red-700 transition">
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-2">
                Welcome, {{ auth()->user()->name }} 👋
            </h2>

            <p class="text-gray-600 mb-6">
                You are successfully logged in to your dashboard.
            </p>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-lg bg-indigo-50">
                    <h3 class="font-semibold text-indigo-700 mb-2">
                        Authentication
                    </h3>
                    <p class="text-sm text-gray-600">
                        This session is secured using Laravel Passport internally.
                    </p>
                </div>

                <div class="p-6 rounded-lg bg-green-50">
                    <h3 class="font-semibold text-green-700 mb-2">
                        User Status
                    </h3>
                    <p class="text-sm text-gray-600">
                        Logged in as <strong>{{ auth()->user()->email }}</strong>
                    </p>
                </div>

                <div class="p-6 rounded-lg bg-yellow-50">
                    <h3 class="font-semibold text-yellow-700 mb-2">
                        Next Step
                    </h3>
                    <p class="text-sm text-gray-600">
                        You can now extend this project to APIs or mobile apps.
                    </p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
```

---

##  Step 10: Define Web Routes

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;


Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth')->group(function () {
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/logout', [AuthController::class, 'logout']);
});

Route::get('/', function () {
    return view('welcome');
});
```

---

##  Project Structure

```
PHP_Laravel12_Passport_Library
│
├── app
│   └── Http
│       └── Controllers
│           └── Web
│               └── AuthController.php
│
├── app
│   └── Models
│       └── User.php
│
├── database
│   └── migrations
│       ├── 0001_01_01_000000_create_users_table.php 
│       ├── 0001_01_01_000001_create_cache_table.php
│       ├── 0001_01_01_000002_create_jobs_table.php
│       ├── 2026_01_23_045937_create_oauth_auth_codes_table.php
│       ├── 2026_01_23_045938_create_oauth_access_tokens_table.php
│       ├── 2026_01_23_045939_create_oauth_refresh_tokens_table.php
│       ├── 2026_01_23_045940_create_oauth_clients_table.php
│       └── 2026_01_23_045941_create_oauth_device_codes_table.php
│
├── resources
│   └── views
│       ├── auth
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       └── dashboard.blade.php
│
├── routes
│   └── web.php
│
├── .env
└── README.md
```

---

## Output

### User Register 

<img width="1802" height="1088" alt="Screenshot 2026-01-23 105723" src="https://github.com/user-attachments/assets/291cac9b-abfb-4bb2-926e-e4d7ab218c1d" />

<img width="1809" height="1089" alt="Screenshot 2026-01-23 105748" src="https://github.com/user-attachments/assets/01864453-14e7-4f57-913e-654052309cbe" />

### User Login

<img width="1812" height="1089" alt="Screenshot 2026-01-23 105826" src="https://github.com/user-attachments/assets/dd0ab4b8-b885-42ad-a855-3d80d4e29cca" />

### User Dashboard 

<img width="1813" height="1089" alt="Screenshot 2026-01-23 105902" src="https://github.com/user-attachments/assets/5757e696-b896-4581-8eec-9b3584907399" />

---

Your PHP_Laravel12_Passport_Library Project is Now Ready!
>>>>>>> development

## Introduction

PHP_Laravel12_Passport_Library is a learning-oriented Laravel project that demonstrates how to integrate Laravel Passport into a normal web-based Laravel 12 application.

Unlike most Passport examples that focus only on API authentication, this project uses Blade views, web routes, and session-based login to explain how Passport works internally within a standard Laravel web application.

The goal of this project is to help developers understand:

- How Laravel Passport is installed and configured
- What OAuth tables Passport creates and why they are needed
- How Passport integrates with Laravel’s default authentication system
- How a web application can be prepared for future API or OAuth expansion

---

## Project Overview

This project is a traditional web authentication system built using Laravel 12 and Laravel Passport.

### Application Features

- User registration
- User login
- Protected dashboard
- Logout functionality

### Passport Usage

Laravel Passport is installed to:

- Generate OAuth client credentials
- Create authentication-related tables (oauth_clients, oauth_access_tokens, etc.)
- Demonstrate how Passport extends Laravel’s authentication ecosystem

---

## Key Characteristics

- Web-based authentication using Blade templates
- Laravel session authentication for login/logout
- Laravel Passport for OAuth infrastructure
- Clean project structure following Laravel 12 standards

---


##  Tech Stack

* Laravel 12
* Laravel Passport
* PHP 8.2+
* MySQL
* Blade Templates
* Web Routes

---

##  Project Name

```
PHP_Laravel12_Passport_Library
```

---

##  Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Passport_Library "12.*"
```

```bash
cd PHP_Laravel12_Passport_Library
```

---

##  Step 2: Environment Configuration

Edit `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_passport_library
DB_USERNAME=root
DB_PASSWORD=
```

Create database `laravel12_passport_library` using migration command

```bash
php artisan migrate
```

---

##  Step 3: Install Laravel Passport

```bash
composer require laravel/passport
```

We only install Passport and use **basic features**, not advanced OAuth flows.

---

##  Step 4: Run Migrations

```bash
php artisan migrate
```

This creates Passport tables internally (OAuth2 infrastructure).

---

##  Step 5: Install Passport Keys

```bash
php artisan passport:install
```

This generates:

* Encryption keys
* OAuth clients (used internally)

---

##  Step 6: Migration Table (Users Default Table)

File: database/migrations/2014_10_12_000000_create_users_table.php

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

##  Step 7: User Model Update

```
app/Models/User.php
```

```php
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

Passport tokens are attached to the user behind the scenes.

---

##  Step 8: Create Web Authentication Controller

```bash
php artisan make:controller Web/AuthController
```

```
app/Http/Controllers/Web/AuthController.php
```

```php
<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        return redirect('/login')->with('success', 'Registered successfully');
    }


    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            return redirect('/dashboard');
        }


        return back()->withErrors(['Invalid credentials']);
    }

    public function dashboard()
    {
        return view('dashboard');
    }


    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
```

---

##  Step 9: Create Blade Views

### 9.1) Register View

```
resources/views/auth/register.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-6">
            <h2 class="text-2xl font-bold text-center text-indigo-600">Create Your Account</h2>

            <!-- Success / Error Messages -->
            @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form -->
            <form method="POST" action="/register" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" name="name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Enter your full name" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white text-lg font-semibold rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                    REGISTER
                </button>
            </form>

            <p class="text-center text-sm text-gray-600">
                Already have an account?
                <a href="/login" class="text-indigo-600 hover:underline font-medium">Login here</a>
            </p>
        </div>
    </div>
</body>
</html>
```

###  9.2) Login View

```
resources/views/auth/login.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-6">
            <h2 class="text-2xl font-bold text-center text-indigo-600">Welcome Back</h2>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded text-sm">
                {{ session('success') }}
            </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="/login" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="you@example.com" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="••••••••" required>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 text-white text-lg font-semibold rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
                    LOGIN
                </button>
            </form>

            <p class="text-center text-sm text-gray-600">
                Don’t have an account?
                <a href="/register" class="text-indigo-600 hover:underline font-medium">Create one</a>
            </p>
        </div>
    </div>
</body>
</html>
```

### 9.3) Dashboard

```
resources/views/dashboard.blade.php
```

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Laravel Passport</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-indigo-600">
                Laravel Passport App
            </h1>

            <a href="/logout"
               class="text-sm font-semibold text-red-600 hover:text-red-700 transition">
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-2">
                Welcome, {{ auth()->user()->name }} 👋
            </h2>

            <p class="text-gray-600 mb-6">
                You are successfully logged in to your dashboard.
            </p>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-lg bg-indigo-50">
                    <h3 class="font-semibold text-indigo-700 mb-2">
                        Authentication
                    </h3>
                    <p class="text-sm text-gray-600">
                        This session is secured using Laravel Passport internally.
                    </p>
                </div>

                <div class="p-6 rounded-lg bg-green-50">
                    <h3 class="font-semibold text-green-700 mb-2">
                        User Status
                    </h3>
                    <p class="text-sm text-gray-600">
                        Logged in as <strong>{{ auth()->user()->email }}</strong>
                    </p>
                </div>

                <div class="p-6 rounded-lg bg-yellow-50">
                    <h3 class="font-semibold text-yellow-700 mb-2">
                        Next Step
                    </h3>
                    <p class="text-sm text-gray-600">
                        You can now extend this project to APIs or mobile apps.
                    </p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
```

---

##  Step 10: Define Web Routes

```
routes/web.php
```

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;


Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);


Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth')->group(function () {
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/logout', [AuthController::class, 'logout']);
});

Route::get('/', function () {
    return view('welcome');
});
```

---

##  Project Structure

```
PHP_Laravel12_Passport_Library
│
├── app
│   └── Http
│       └── Controllers
│           └── Web
│               └── AuthController.php
│
├── app
│   └── Models
│       └── User.php
│
├── database
│   └── migrations
│       ├── 0001_01_01_000000_create_users_table.php 
│       ├── 0001_01_01_000001_create_cache_table.php
│       ├── 0001_01_01_000002_create_jobs_table.php
│       ├── 2026_01_23_045937_create_oauth_auth_codes_table.php
│       ├── 2026_01_23_045938_create_oauth_access_tokens_table.php
│       ├── 2026_01_23_045939_create_oauth_refresh_tokens_table.php
│       ├── 2026_01_23_045940_create_oauth_clients_table.php
│       └── 2026_01_23_045941_create_oauth_device_codes_table.php
│
├── resources
│   └── views
│       ├── auth
│       │   ├── login.blade.php
│       │   └── register.blade.php
│       └── dashboard.blade.php
│
├── routes
│   └── web.php
│
├── .env
└── README.md
```

---

## Output

### User Register 

<img width="1802" height="1088" alt="Screenshot 2026-01-23 105723" src="https://github.com/user-attachments/assets/291cac9b-abfb-4bb2-926e-e4d7ab218c1d" />

<img width="1809" height="1089" alt="Screenshot 2026-01-23 105748" src="https://github.com/user-attachments/assets/01864453-14e7-4f57-913e-654052309cbe" />

### User Login

<img width="1812" height="1089" alt="Screenshot 2026-01-23 105826" src="https://github.com/user-attachments/assets/dd0ab4b8-b885-42ad-a855-3d80d4e29cca" />

### User Dashboard 

<img width="1813" height="1089" alt="Screenshot 2026-01-23 105902" src="https://github.com/user-attachments/assets/5757e696-b896-4581-8eec-9b3584907399" />

---

Your PHP_Laravel12_Passport_Library Project is Now Ready!
