<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - ' . app_name())</title>
    <link rel="icon" href="{{ app_favicon() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html { background: #f8fafc; }
        body { font-family: 'Inter', sans-serif; visibility: hidden; opacity: 0; transition: opacity 0.25s ease-in-out, visibility 0s linear 0.25s; }
        body.show-content { visibility: visible; opacity: 1; transition: opacity 0.25s ease-in-out; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .sidebar-transition { transition: width 0.3s ease, transform 0.3s ease; }
        [x-cloak] { display: none !important; }
        
        /* Sidebar slide animation */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }

        /* Prevent FOUC - hide content until Alpine is ready */
        .alpine-init { display: none; }
        .alpine-init.x-init { display: block; }

        /* Hover effects for cards */
        .hover-lift {
            transition: all 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Touch-friendly buttons */
        @media (max-width: 640px) {
            .btn-touch {
                min-height: 44px;
                min-width: 44px;
                padding: 0.625rem 1rem;
            }
            input, select, textarea {
                font-size: 16px !important;
            }
        }

        /* Custom scrollbar for tables */
        .scrollbar-thin {
            scrollbar-width: thin;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-100 overflow-x-hidden">
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-700 flex flex-col shadow-xl overflow-hidden transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <div class="h-14 sm:h-16 flex items-center justify-between px-3 sm:px-4 border-b border-gray-700" style="background: linear-gradient(to right, {{ primary_color() }}, {{ system_setting("secondary_color", "#10B981") }})">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-8 sm:w-10 h-8 sm:h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if(current_school()?->logo)
                            <img src="{{ current_school()->logoUrl }}" alt="Logo" class="w-6 sm:w-8 h-6 sm:h-8 object-contain">
                        @else
                            <span class="text-[{{ primary_color() }}] font-bold text-sm sm:text-lg">{{ substr(app_name(), 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="hidden sidebar-expanded:block">
                        <h1 class="text-white font-bold text-sm sm:text-lg leading-tight truncate max-w-[120px] sm:max-w-none">{{ app_name() }}</h1>
                        <p class="text-white/70 text-xs hidden sm:block">Admission Portal</p>
                    </div>
                </div>
                <button id="closeSidebar" class="lg:hidden text-white hover:text-blue-200 p-1">
                    <svg class="w-5 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Navigation --}}
            <nav class="flex-1 py-3 sm:py-4 overflow-y-auto">
                <div class="px-2 sm:px-3 mb-2">
                    <p class="px-2 sm:px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main Menu</p>
                </div>
                
                <div class="space-y-0.5 sm:space-y-1 px-2 sm:px-3">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-home w-5 sm:w-6 text-center text-sm sm:text-base"></i>
                        <span class="sidebar-text ml-2 sm:ml-3 font-medium text-sm sm:text-base">Dashboard</span>
                    </a>
                    
                    @can('view_applications')
                    <a href="{{ route('admin.applications.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.applications.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-file-alt w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Applications</span>
                    </a>
                    @endcan
                    
                    @can('view_reports')
                    <a href="{{ route('admin.analytics') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.analytics') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-chart-line w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Analytics</span>
                    </a>
                    @endcan
                </div>
                
                <div class="px-3 mt-6 mb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</p>
                </div>
                
                <div class="space-y-1 px-3">
                    @can('view_users')
                    <a href="{{ route('admin.users.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-users w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Users</span>
                    </a>
                    @endcan
                    
                    @can('view_programs')
                    <a href="{{ route('admin.programs.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.programs.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-graduation-cap w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Programs</span>
                    </a>
                    @endcan
                    
                    @can('manage_intakes')
                    <a href="{{ route('admin.intakes.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.intakes.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-calendar-alt w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Intakes</span>
                    </a>
                    @endcan
                    
                    @can('view_admission_letters')
                    <a href="{{ route('admin.admission-letters.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.admission-letters.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-envelope-open-text w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Admission Letters</span>
                    </a>
                    @endcan
                    
                    @can('view_documents')
                    <a href="{{ route('admin.documents.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.documents.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-file-check w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Document Verification</span>
                    </a>
                    @endcan
                    
                    @can('send_notifications')
                    <a href="{{ route('admin.notifications.send') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.notifications.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-bullhorn w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Notifications</span>
                    </a>
                    @endcan
                    
                    @can('send_broadcast')
                    <a href="{{ route('admin.broadcast.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.broadcast.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-broadcast-tower w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Broadcasts</span>
                    </a>
                    @endcan
                    
                    <a href="{{ route('admin.messages.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.messages.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-comment-dots w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Messages</span>
                    </a>
                    
                    @can('view_inquiries')
                    <a href="{{ route('admin.inquiries.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.inquiries.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-headset w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Inquiries</span>
                    </a>
                    @endcan
                    
                    @can('view_audit_logs')
                    <a href="{{ route('admin.audit-logs.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-history w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Audit Logs</span>
                    </a>
                    @endcan

                    @canany(['view_roles', 'create_role', 'edit_role', 'delete_role', 'assign_roles'])
                    <a href="{{ route('admin.roles.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-shield-alt w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Roles & Permissions</span>
                    </a>
                    @endcanany
                    
                    @can('manage_form_fields')
                    <a href="{{ route('admin.form-fields.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.form-fields.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-th-list w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Form Fields</span>
                    </a>
                    @endcan
                    
                    @can('manage_faqs')
                    <a href="{{ route('admin.faqs.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.faqs.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-question-circle w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">FAQs</span>
                    </a>
                    @endcan
                    
                    @can('view_financial_summary')
                    <a href="{{ route('admin.accountant.payments') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.accountant.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-file-invoice-dollar w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Financial Reports</span>
                    </a>
                    @endcan
                </div>
                
                <div class="px-3 mt-6 mb-2">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings</p>
                </div>
                
                <div class="space-y-1 px-3">
                    <a href="{{ route('admin.subscription.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.subscription.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-gem w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">My Subscription</span>
                    </a>
                    
                    @role('accountant|finance')
                    <a href="{{ route('admin.accountant.dashboard') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.accountant.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-chart-pie w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Finance</span>
                    </a>
                    @endrole
                    
                    @role('reviewer')
                    <a href="{{ route('admin.reviewer.dashboard') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.reviewer.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-clipboard-check w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Review Queue</span>
                    </a>
                    @endrole
                    
                    @can('view_settings')
                    <a href="{{ route('admin.settings') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-cog w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">School Settings</span>
                    </a>
                    @endcan
                    
                    @can('view_settings')
                    <a href="{{ route('admin.ai-settings.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.ai-settings.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-robot w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">AI Settings</span>
                    </a>
                    @endcan

                    @can('ai.view_conversations')
                    <a href="{{ route('admin.ai.conversations.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.ai.conversations.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-comments w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">AI Conversations</span>
                    </a>
                    @endcan

                    @can('ai.view_leads')
                    <a href="{{ route('admin.ai.leads.index') }}" class="nav-link flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.ai.leads.*') ? 'bg-[#00008B] text-white shadow-md' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-user-plus w-6 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">AI Leads</span>
                    </a>
                    @endcan
                </div>
            </nav>
            
            {{-- Sidebar Footer --}}
            <div class="p-4 border-t border-gray-700 bg-gray-800">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center">
                            <x-user-avatar :user="auth()->user()" :size="40" class="" />
                        </div>
                        <div class="sidebar-expanded ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->fullName() }}</p>
                            <p class="text-xs text-gray-400">
                                @foreach(auth()->user()->getRoleNames() as $role)
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('student.dashboard') }}" class="text-center py-2 bg-gray-700 border border-gray-600 rounded-lg text-xs font-medium text-gray-300 hover:bg-gray-600 transition-colors">
                        <i class="fas fa-user-graduate mr-1"></i> Student
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-red-600 rounded-lg text-xs font-medium text-white hover:bg-red-700 transition-colors">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Mobile Sidebar Overlay --}}
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

        {{-- Main Content Wrapper --}}
        <div id="mainWrapper" class="flex-1 flex flex-col min-h-screen lg:ml-64 overflow-x-hidden min-w-0">
            {{-- Top Header --}}
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="h-14 sm:h-16 px-3 sm:px-6 flex items-center justify-between">
                    {{-- Left: Menu Toggle & Title --}}
                    <div class="flex items-center min-w-0">
                        <button id="openSidebar" class="lg:hidden mr-2 sm:mr-4 p-1.5 sm:p-2 text-gray-500 hover:text-[#00008B] hover:bg-gray-100 rounded-lg transition-colors flex-shrink-0">
                            <svg class="w-5 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button id="collapseSidebar" class="hidden lg:block p-2 text-gray-500 hover:text-[#00008B] hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-xl font-semibold text-gray-900 truncate">@yield('header', 'Dashboard')</h2>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="flex items-center gap-1 sm:gap-3">
                        {{-- Notifications --}}
                        <x-notification-dropdown :url="route('admin.notifications.index')" />

                        {{-- Messages - Hidden on mobile --}}
                        <div class="hidden sm:block">
                            <x-chat-dropdown />
                        </div>

                        {{-- Language Switcher - Hidden on mobile --}}
                        <div class="hidden lg:flex items-center bg-gray-100 rounded-lg p-1">
                            <a href="{{ route('lang', 'en') }}" class="px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm font-medium transition-colors {{ app()->getLocale() == 'en' ? 'bg-[#00008B] text-white shadow-sm' : 'text-gray-600 hover:text-[#00008B]' }}">
                                EN
                            </a>
                            <a href="{{ route('lang', 'sw') }}" class="px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm font-medium transition-colors {{ app()->getLocale() == 'sw' ? 'bg-[#00008B] text-white shadow-sm' : 'text-gray-600 hover:text-[#00008B]' }}">
                                SW
                            </a>
                        </div>

                        {{-- User Profile Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center p-1 hover:bg-gray-100 rounded-lg transition-colors">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
                                    <x-user-avatar :user="auth()->user()" :size="32" class="" />
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block ml-1"></i>
                            </button>
                            <div x-show="open" x-cloak @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->fullName() }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-user mr-2"></i> My Profile
                                </a>
                                <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-cog mr-2"></i> Settings
                                </a>
                                <div class="border-t border-gray-100 mt-2 pt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            <div class="px-3 sm:px-4 lg:px-6 pt-3 sm:pt-4">
                @if(session('toastr'))
                    @php $toastr = session('toastr'); @endphp
                    <div class="mb-3 sm:mb-4 bg-{{ $toastr['type'] == 'success' ? 'green' : ($toastr['type'] == 'error' ? 'red' : ($toastr['type'] == 'warning' ? 'yellow' : 'blue')) }}-50 border border-{{ $toastr['type'] == 'success' ? 'green' : ($toastr['type'] == 'error' ? 'red' : ($toastr['type'] == 'warning' ? 'yellow' : 'blue')) }}-200 text-{{ $toastr['type'] == 'success' ? 'green' : ($toastr['type'] == 'error' ? 'red' : ($toastr['type'] == 'warning' ? 'yellow' : 'blue')) }}-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg flex items-start sm:items-center animate-slide-in">
                        <i class="fas fa-{{ $toastr['type'] == 'success' ? 'check-circle' : ($toastr['type'] == 'error' ? 'exclamation-circle' : ($toastr['type'] == 'warning' ? 'exclamation-triangle' : 'info-circle')) }} mr-2 sm:mr-3 text-{{ $toastr['type'] == 'success' ? 'green' : ($toastr['type'] == 'error' ? 'red' : ($toastr['type'] == 'warning' ? 'yellow' : 'blue')) }}-600 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                        <span class="text-xs sm:text-sm">{{ $toastr['message'] }}</span>
                        <button type="button" class="ml-auto hover:opacity-70 flex-shrink-0" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                @if(session('success') && !session('toastr'))
                <div class="mb-3 sm:mb-4 bg-green-50 border border-green-200 text-green-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg flex items-start sm:items-center animate-slide-in">
                    <i class="fas fa-check-circle mr-2 sm:mr-3 text-green-600 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                    <span class="text-xs sm:text-sm">{{ session('success') }}</span>
                    <button type="button" class="ml-auto text-green-600 hover:text-green-800 flex-shrink-0" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif
                
                @if(session('error') && !session('toastr'))
                <div class="mb-3 sm:mb-4 bg-red-50 border border-red-200 text-red-800 px-3 sm:px-4 py-2 sm:py-3 rounded-lg flex items-start sm:items-center animate-slide-in">
                    <i class="fas fa-exclamation-circle mr-2 sm:mr-3 text-red-600 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                    <span class="text-xs sm:text-sm">{{ session('error') }}</span>
                    <button type="button" class="ml-auto text-red-600 hover:text-red-800 flex-shrink-0" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif
            </div>
            
            {{-- Page Content --}}
            <div class="flex-1 p-3 sm:p-4 lg:p-6 overflow-x-hidden">
                @yield('content')
            </div>
            
            {{-- Footer --}}
            <footer class="bg-white border-t border-gray-200 px-6 py-4 mt-auto">
                <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500">
                    <p>© {{ date('Y') }} {{ app_name() }}. All rights reserved.</p>
                    @if(app_footer())
                        <span class="text-xs text-gray-400 mt-2 sm:mt-0">
                            Powered by <a href="https://duncowebsolutions.com" target="_blank" class="hover:text-gray-600 transition-colors">Duncowebsolutions</a>
                        </span>
                    @endif
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Console logging enabled for debugging
        window.console.log('Admin layout loaded');
        
        // Global error handler
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('Global Error:', { message: msg, url: url, line: lineNo, column: columnNo, error: error });
            return false;
        };
        
        // Uncaught promise rejection handler
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled Promise Rejection:', event.reason);
        });

        // Check if Alpine is loaded
        document.addEventListener('alpine:init', function() {
            console.log('Alpine.js initialized');
        });

        window.addEventListener('load', function() {
            document.body.classList.add('show-content');
        });
        
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-bottom-right',
            timeOut: 3000,
            extendedTimeOut: 1000,
        };

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const openSidebarBtn = document.getElementById('openSidebar');
            const closeSidebarBtn = document.getElementById('closeSidebar');
            const collapseSidebarBtn = document.getElementById('collapseSidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            function toggleSidebarCollapse() {
                const collapsed = sidebar.classList.contains('w-20');
                if (collapsed) {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.sidebar-expanded').forEach(el => el.classList.remove('hidden'));
                } else {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.sidebar-expanded').forEach(el => el.classList.add('hidden'));
                }
            }

            openSidebarBtn?.addEventListener('click', openSidebar);
            closeSidebarBtn?.addEventListener('click', closeSidebar);
            sidebarOverlay?.addEventListener('click', closeSidebar);
            collapseSidebarBtn?.addEventListener('click', toggleSidebarCollapse);
            
            // Close sidebar on navigation (mobile)
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        closeSidebar();
                    }
                });
            });
            
            // Handle resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });
        });

        // Global error handler - catch third-party script errors without breaking page
        window.addEventListener('error', function(e) {
            if (e.filename && (e.filename.includes('giveFreely') || e.filename.includes('toastr'))) {
                console.warn('Third-party script error caught:', e.message);
                e.preventDefault();
                return false;
            }
        }, true);

        // Handle unhandled promise rejections gracefully
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason && event.reason.message && event.reason.message.includes('payload')) {
                console.warn('Unhandled promise rejection handled:', event.reason.message);
                event.preventDefault();
            }
        });
    </script>
    <style>
        @keyframes slide-in {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slide-in 0.3s ease-out; }
    </style>
    @stack('scripts')
</body>
</html>
