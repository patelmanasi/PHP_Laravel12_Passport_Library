<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800 flex justify-center items-start min-h-screen py-10">

    <div class="max-w-5xl w-full bg-white rounded-2xl shadow-xl p-8">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-600">Users</h1>

        <!-- Success / Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6 text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Search -->
        <form method="GET" class="mb-6 flex flex-col md:flex-row gap-3">
            <input type="text" name="search" placeholder="Search users"
                class="border border-gray-300 rounded-lg px-4 py-2 flex-1 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 transition">
                Search
            </button>
            <a href="/users/export"
                class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition text-center">
                Export CSV
            </a>
        </form>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto rounded-xl border border-gray-200 shadow-md overflow-hidden">
                <thead>
                    <tr class="bg-indigo-600 text-white text-left">
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50 @if($user->trashed()) bg-red-50 @endif">
                            <td class="px-6 py-3">{{ $user->id }}</td>
                            <td class="px-6 py-3">{{ $user->name }}</td>
                            <td class="px-6 py-3">{{ $user->email }}</td>
                            <td class="px-6 py-3">
                                @if($user->trashed())
                                    <span class="text-red-600 font-semibold">Deleted</span>
                                @else
                                    {{ $user->status ? 'Active' : 'Inactive' }}
                                @endif
                            </td>
                            <td class="px-6 py-3 space-x-2">
                                @if(!$user->trashed())
                                    <a href="/users/toggle-status/{{ $user->id }}"
                                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
                                        Toggle Status
                                    </a>
                                    <a href="/users/delete/{{ $user->id }}"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition"
                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                        Delete
                                    </a>
                                @else
                                    <a href="/users/restore/{{ $user->id }}"
                                        class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                                        Restore
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $users->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- Custom Pagination Colors -->
    <style>
        /* Default pagination link style */
        ul.pagination li span,
        ul.pagination li a {
            @apply mx-1 px-3 py-1 rounded-lg text-gray-700 hover:text-white hover:bg-indigo-600 transition;
        }

        /* Active page number */
        ul.pagination li.active span {
            @apply bg-indigo-600 text-white font-semibold;
        }

        /* Disabled link */
        ul.pagination li.disabled span {
            @apply text-gray-400;
        }
    </style>

</body>

</html>