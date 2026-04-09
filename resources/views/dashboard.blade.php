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

            <a href="/logout" class="text-sm font-semibold text-red-600 hover:text-red-700 transition">
                Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-bold mb-4">
                Welcome, {{ auth()->user()->name }} 👋
            </h2>

            <p class="text-gray-600 mb-6">
                You are successfully logged in to your dashboard.
            </p>

            <!-- Manage Users Button -->
            <div class="mb-6">
                <a href="/users"
                    class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                    Manage Users
                </a>
            </div>

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