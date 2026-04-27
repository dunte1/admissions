<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', app_name() . ' - Student Portal')</title>
    <link rel="icon" href="{{ app_favicon() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-screen flex flex-col">
        {{-- Top Navigation --}}
        <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    {{-- Logo --}}
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-md overflow-hidden" style="background: linear-gradient(to bottom right, {{ primary_color() }}, {{ system_setting('secondary_color', '#10B981') }})">
                                @if(current_school()?->logo)
                                    <img src="{{ current_school()->logoUrl }}" alt="Logo" class="w-8 h-8 object-contain bg-white">
                                @else
                                    <span class="text-white font-bold text-lg">{{ substr(app_name(), 0, 1) }}</span>
                                @endif
                            </div>
                            <div>
                                <span class="text-xl font-bold text-gray-900">{{ app_name() }}</span>
                                <span class="hidden sm:block text-xs text-gray-500">Student Portal</span>
                            </div>
                        </a>
                    </div>
                    
                    {{-- Right Side --}}
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('student.dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student.dashboard') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-home mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('student.application.create') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student.application.create') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-plus mr-2"></i>New Application
                        </a>
                        <a href="{{ route('student.offers.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student.offers.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-envelope-open-text mr-2"></i>My Offers
                        </a>
                        <a href="{{ route('student.faqs.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('student.faqs.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-question-circle mr-2"></i>FAQs
                        </a>
                        
                        {{-- Notifications --}}
                        <x-notification-dropdown :url="route('student.notifications.index')" />
                        
                        {{-- Messages --}}
                        <a href="{{ route('student.messages.index') }}" class="relative p-2 text-gray-500 hover:text-purple-600 hover:bg-gray-100 rounded-lg transition-colors {{ request()->routeIs('student.messages.*') ? 'text-purple-600 bg-purple-50' : '' }}">
                            <i class="fas fa-comment-dots text-lg"></i>
                        </a>
                        
                        {{-- Language Switcher --}}
                        <div class="flex items-center bg-gray-100 rounded-lg p-1 ml-2">
                            <a href="{{ route('lang', 'en') }}" class="px-3 py-1 rounded-md text-sm font-medium transition-colors {{ app()->getLocale() == 'en' ? 'bg-[#00008B] text-white shadow-sm' : 'text-gray-600 hover:text-[#00008B]' }}">
                                EN
                            </a>
                            <a href="{{ route('lang', 'sw') }}" class="px-3 py-1 rounded-md text-sm font-medium transition-colors {{ app()->getLocale() == 'sw' ? 'bg-[#00008B] text-white shadow-sm' : 'text-gray-600 hover:text-[#00008B]' }}">
                                SW
                            </a>
                        </div>
                        
                        {{-- User Menu --}}
                        <div class="relative ml-2" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-100 rounded-lg px-3 py-2 transition-colors">
                                <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center">
                                    <x-user-avatar :user="auth()->user()" :size="40" class="" />
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->first_name }}</p>
                                    <p class="text-xs text-gray-500">Student</p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->fullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('student.profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user w-6 text-gray-400"></i>
                                    {{ __('labels.edit_profile') }}
                                </a>
                                <a href="{{ route('student.notifications.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-bell w-6 text-gray-400"></i>
                                    Notifications
                                    @isset($unreadCount)
                                    @if($unreadCount > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                    @endif
                                    @endisset
                                </a>
                                <a href="{{ route('student.dashboard') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-home w-6 text-gray-400"></i>
                                    Dashboard
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-6 text-red-400"></i>
                                            {{ __('labels.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile: Notification Bell & Profile --}}
                    <div class="flex items-center md:hidden space-x-1">
                        <button @click="toggleNotifications()" class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell text-lg"></i>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" class="flex items-center focus:outline-none">
                                <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center ring-2 ring-gray-200">
                                    <x-user-avatar :user="auth()->user()" :size="32" class="" />
                                </div>
                            </button>
                            <div x-show="profileOpen" x-cloak @click.away="profileOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->fullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('student.profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user w-6 text-gray-400"></i>Profile
                                </a>
                                <a href="{{ route('student.notifications.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-bell w-6 text-gray-400"></i>Notifications
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt w-6 text-red-400"></i>Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="open" x-cloak @click.away="open = false" x-transition class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-home mr-3"></i>Dashboard
                    </a>
                    <a href="{{ route('student.application.create') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-plus mr-3"></i>New Application
                    </a>
                    <a href="{{ route('student.faqs.index') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-question-circle mr-3"></i>FAQs
                    </a>
                    <a href="{{ route('student.profile.edit') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-3"></i>Profile
                    </a>
                    <a href="{{ route('student.notifications.index') }}" class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-bell mr-3"></i>Notifications
                        @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    <div class="flex items-center space-x-4 pt-2 border-t border-gray-200">
                        <a href="{{ route('lang', 'en') }}" class="px-3 py-1 rounded text-sm {{ app()->getLocale() == 'en' ? 'bg-[#00008B] text-white' : 'bg-gray-100' }}">EN</a>
                        <a href="{{ route('lang', 'sw') }}" class="px-3 py-1 rounded text-sm {{ app()->getLocale() == 'sw' ? 'bg-[#00008B] text-white' : 'bg-gray-100' }}">SW</a>
                    </div>
                    <div class="pt-2 border-t border-gray-200">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                                <i class="fas fa-sign-out-alt mr-3"></i>{{ __('labels.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        
        {{-- Flash Messages --}}
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 w-full pt-4 sm:pt-6">
            @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                <span class="text-sm">{{ session('success') }}</span>
                <button type="button" class="ml-auto text-green-600 hover:text-green-800" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
                <span class="text-sm">{{ session('error') }}</span>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @if(session('warning'))
            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                <i class="fas fa-exclamation-triangle mr-3 text-yellow-600"></i>
                <span class="text-sm">{{ session('warning') }}</span>
                <button type="button" class="ml-auto text-yellow-600 hover:text-yellow-800" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
        </div>
        
        {{-- Main Content --}}
        <main class="flex-1 pb-6 sm:pb-8">
            @yield('content')
        </main>
        
        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 px-6 py-4 mt-auto">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
                    <p>© {{ date('Y') }} {{ app_name() }}. All rights reserved.</p>
                    @if(app_footer())
                        <span class="text-xs text-gray-400 mt-2 sm:mt-0">
                            Powered by <a href="https://duncowebsolutions.com" target="_blank" class="hover:text-gray-600 transition-colors">Duncowebsolutions</a>
                        </span>
                    @endif
                </div>
            </div>
        </footer>
    </div>
    
    <script>
        function toggleNotifications() {
            const dropdown = document.querySelector('.notification-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }

        window.onerror = function(msg, url, lineNo, columnNo, error) {
            if (msg.includes('giveFreely') || msg.includes('payload')) {
                return true;
            }
            console.error('Global error:', msg, 'at', url, ':', lineNo, ':', columnNo);
            return false;
        };
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason && event.reason.message && (event.reason.message.includes('giveFreely') || event.reason.message.includes('payload'))) {
                event.preventDefault();
            }
        });
    </script>
    <x-ai-chat-widget mode="system" />
    <style>
        @keyframes slide-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slide-in 0.3s ease-out; }
    </style>
    @stack('scripts')
</body>
</html>
