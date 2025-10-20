<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Nura Oud Essence</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="bg-white shadow-lg w-64 flex-shrink-0 hidden md:flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">Nura Oud Essence</h1>
                <p class="text-sm text-gray-600">{{ ucfirst(Auth::user()->role) }} Panel</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6">
                <ul class="space-y-2">
                    @php
                        $currentRoute = request()->route()->getName();

                        $menuItems = [
                            [
                                'name' => 'Dashboard',
                                'route' => 'admin.dashboard',
                                'icon' => 'home',
                                'active' => $currentRoute === 'admin.dashboard'
                            ],
                            [
                                'name' => 'Products',
                                'route' => 'admin.products',
                                'icon' => 'cube',
                                'active' => request()->routeIs('admin.products*')
                            ],
                            [
                                'name' => 'Orders',
                                'route' => 'admin.orders',
                                'icon' => 'clipboard-document-list',
                                'active' => request()->routeIs('admin.orders*') && !request()->routeIs('admin.orders.chat')
                            ],
                            [
                                'name' => 'Chats',
                                'route' => 'admin.chats',
                                'icon' => 'chat-bubble-left-right',
                                'active' => request()->routeIs('admin.chats') || request()->routeIs('admin.orders.chat')
                            ],
                            [
                                'name' => 'Vouchers',
                                'route' => 'admin.vouchers.index',
                                'icon' => 'ticket',
                                'active' => request()->routeIs('admin.vouchers*')
                            ]
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200 {{ $item['active'] ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : '' }}">
                                <span class="w-5 h-5 mr-3">
                                    @switch($item['icon'])
                                        @case('home')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            @break
                                        @case('cube')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            @break
                                        @case('clipboard-document-list')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            @break
                                        @case('chat-bubble-left-right')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            @break
                                        @case('ticket')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            @break
                                    @endswitch
                                </span>
                                {{ $item['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="fixed inset-y-0 left-0 bg-white shadow-lg w-64 z-50 transform -translate-x-full md:hidden transition-transform duration-300">
            <!-- Mobile Logo -->
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">Nura Oud Essence</h1>
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex-1 px-4 py-6">
                <ul class="space-y-2">
                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200 {{ $item['active'] ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-700' : '' }}">
                                <span class="w-5 h-5 mr-3">
                                    @switch($item['icon'])
                                        @case('home')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            @break
                                        @case('cube')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            @break
                                        @case('clipboard-document-list')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            @break
                                        @case('chat-bubble-left-right')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            @break
                                        @case('ticket')
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                            @break
                                    @endswitch
                                </span>
                                {{ $item['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Mobile User Info & Logout -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center justify-between md:hidden">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">@yield('title')</h1>
                <div class="w-6"></div> <!-- Spacer -->
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const isOpen = !sidebar.classList.contains('-translate-x-full');

            if (isOpen) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
        }
    </script>

    <script>
        // Poll admin unread chat count and render a red badge next to the Chats menu item
        async function fetchUnreadCount() {
            try {
                const res = await fetch("{{ url('/admin/chats/unread-count') }}", {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!res.ok) return;
                const data = await res.json();
                const count = Number(data.unread || 0);
                const navLinks = document.querySelectorAll('#sidebar nav ul li a');
                navLinks.forEach(a => {
                    if (a.getAttribute('href') && a.getAttribute('href').includes('/admin/chats')) {
                        // Ensure badge container
                        let badge = a.querySelector('.unread-badge');
                        if (count > 0) {
                            if (!badge) {
                                badge = document.createElement('span');
                                badge.className = 'unread-badge ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium leading-4 rounded-full bg-red-600 text-white';
                                a.appendChild(badge);
                            }
                            badge.textContent = count > 99 ? '99+' : String(count);
                        } else if (badge) {
                            badge.remove();
                        }
                    }
                });
            } catch (e) {
                // ignore network errors silently
            }
        }

        // Initial fetch and periodic polling every 20s
        if (document.body) {
            fetchUnreadCount();
            setInterval(fetchUnreadCount, 20000);
        }
    </script>

    @livewireScripts
</body>
</html>
