@extends('layouts.guest')

@section('content')
<style>
    * { font-family: 'Inter', sans-serif; }
    .brand-gradient {
        background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}cc 50%, {{ $primaryColor }}99 100%);
    }
    .brand-gradient-light {
        background: linear-gradient(135deg, {{ $primaryColor }}0d 0%, {{ $primaryColor }}05 100%);
    }
    .btn-primary {
        background: linear-gradient(135deg, {{ $primaryColor }} 0%, {{ $primaryColor }}cc 100%);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, {{ $primaryColor }}cc 0%, {{ $primaryColor }}99 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px {{ $primaryColor }}4d;
    }
    .glass-nav {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .floating {
        animation: floating 3s ease-in-out infinite;
    }
    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .school-card {
        transition: all 0.3s ease;
    }
    .school-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .testimonial-card {
        transition: all 0.3s ease;
    }
    .testimonial-card:hover {
        transform: translateY(-4px);
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<div class="min-h-screen bg-gray-50 relative z-40">
    {{-- Navigation --}}
    <nav class="glass-nav sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    @if($systemLogo)
                        <img src="{{ asset('storage/' . $systemLogo) }}" alt="{{ $systemName }}" class="h-10 w-auto">
                    @else
                        <div class="w-10 h-10 brand-gradient rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl font-bold" style="color: {{ $primaryColor }}">{{ $systemName }}</span>
                </a>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-gray-600 hover:text-[{{ $primaryColor }}] transition-colors font-medium text-sm">Features</a>
                    <a href="#schools" class="text-gray-600 hover:text-[{{ $primaryColor }}] transition-colors font-medium text-sm">Schools</a>
                    <a href="#programs" class="text-gray-600 hover:text-[{{ $primaryColor }}] transition-colors font-medium text-sm">Programs</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-[{{ $primaryColor }}] transition-colors font-medium text-sm">How It Works</a>
                    <a href="{{ route('login') }}" class="text-[{{ $primaryColor }}] hover:text-[{{ $primaryColor }}] transition-colors font-medium text-sm">Sign In</a>
                    <a href="{{ route('login') }}" class="btn-primary text-white px-5 py-2.5 rounded-lg font-medium text-sm shadow-md">
                        Get Started
                    </a>
                    <div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-1.5">
                        <a href="{{ route('lang', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'font-bold' : 'text-gray-500 hover:text-gray-700' }}" style="color: {{ app()->getLocale() == 'en' ? $primaryColor : '#6b7280' }}; font-size: 12px;">EN</a>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('lang', 'sw') }}" class="{{ app()->getLocale() == 'sw' ? 'font-bold' : 'text-gray-500 hover:text-gray-700' }}" style="color: {{ app()->getLocale() == 'sw' ? $primaryColor : '#6b7280' }}; font-size: 12px;">SW</a>
                    </div>
                </div>
                <button id="mobile-menu-btn" class="md:hidden" style="color: {{ $primaryColor }}">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-3">
                <a href="#features" class="block text-gray-600 hover:text-[{{ $primaryColor }}]">Features</a>
                <a href="#schools" class="block text-gray-600 hover:text-[{{ $primaryColor }}]">Schools</a>
                <a href="#programs" class="block text-gray-600 hover:text-[{{ $primaryColor }}]">Programs</a>
                <a href="#how-it-works" class="block text-gray-600 hover:text-[{{ $primaryColor }}]">How It Works</a>
                <a href="{{ route('login') }}" class="block font-medium" style="color: {{ $primaryColor }}">Sign In</a>
                <a href="{{ route('login') }}" class="block font-medium" style="color: {{ $primaryColor }}">Get Started</a>
                <div class="flex items-center space-x-4 pt-2">
                    <a href="{{ route('lang', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'font-bold' : 'text-gray-500' }}" style="color: {{ app()->getLocale() == 'en' ? $primaryColor : '#6b7280' }}">EN</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang', 'sw') }}" class="{{ app()->getLocale() == 'sw' ? 'font-bold' : 'text-gray-500' }}" style="color: {{ app()->getLocale() == 'sw' ? $primaryColor : '#6b7280' }}">SW</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <div class="relative brand-gradient overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
            <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-white/5 rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative">
                <div class="text-center fade-in-up">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 leading-tight tracking-tight">
                    {{ $systemName }}
                </h1>
                <p class="text-xl md:text-2xl text-white/80 mb-6 font-light">{{ $tagline }}</p>
                <p class="text-lg text-white/70 mb-10 max-w-2xl mx-auto">
                    A modern, multi-school admission portal designed to streamline applications, payments, and student tracking for institutions across {{ $stats['schools'] }} schools.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('login') }}" class="group bg-white px-8 py-4 rounded-xl font-semibold text-base shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all flex items-center space-x-2 w-full sm:w-auto justify-center" style="color: {{ $primaryColor }}">
                        <span>Apply Now ({{ $currentYear }} Intake)</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route('login') }}" class="group border-2 border-white/30 text-white px-8 py-4 rounded-xl font-semibold text-base hover:bg-white/10 transition-all flex items-center space-x-2 w-full sm:w-auto justify-center">
                        <i class="fas fa-user-circle"></i>
                        <span>School Login</span>
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="group border-2 border-white/30 text-white px-8 py-4 rounded-xl font-semibold text-base hover:bg-white/10 transition-all flex items-center space-x-2 w-full sm:w-auto justify-center">
                        <i class="fas fa-calendar"></i>
                        <span>Request Demo</span>
                    </a>
                </div>

                <div class="mt-8 inline-flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
                    <i class="fas fa-shield-alt text-white/80"></i>
                    <span class="text-white/80 text-sm">Trusted by {{ $stats['schools'] }}+ institutions nationwide</span>
                </div>

                <div class="mt-12 flex items-center justify-center space-x-8 md:space-x-16 text-white/80">
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-white">{{ number_format($stats['students']) }}+</div>
                        <div class="text-sm mt-1 text-white/60">Students</div>
                    </div>
                    <div class="w-px h-12 bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-white">{{ number_format($stats['programs']) }}+</div>
                        <div class="text-sm mt-1 text-white/60">Programs</div>
                    </div>
                    <div class="w-px h-12 bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-white">{{ number_format($stats['applications']) }}+</div>
                        <div class="text-sm mt-1 text-white/60">Applications</div>
                    </div>
                    <div class="w-px h-12 bg-white/20"></div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-white">{{ $stats['success_rate'] }}</div>
                        <div class="text-sm mt-1 text-white/60">Success Rate</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-gray-50 to-transparent"></div>
    </div>

    {{-- Features Section --}}
    <div id="features" class="bg-gray-50 py-20 relative z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">Powerful Features</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Everything you need to manage school admissions efficiently and professionally.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-laptop-code text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Online Applications</h3>
                    <p class="text-gray-600">Students can apply online from anywhere, anytime. Upload documents, track status, and receive updates in real-time.</p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Multi-School Support</h3>
                    <p class="text-gray-600">Manage multiple schools, colleges, or universities from a single platform with complete data isolation.</p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-mobile-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">MPESA Payments</h3>
                    <p class="text-gray-600">Integrated M-PESA payment gateway for easy application fee collection. Instant notifications and reconciliation.</p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Real-time Tracking</h3>
                    <p class="text-gray-600">Track application status in real-time. Get notified at every stage from submission to admission decision.</p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Role-based Dashboards</h3>
                    <p class="text-gray-600">Custom dashboards for administrators, registrars, finance officers, and reviewers with appropriate permissions.</p>
                </div>

                <div class="group bg-white rounded-2xl p-8 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-gray-100">
                    <div class="w-14 h-14 brand-gradient rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Document Management</h3>
                    <p class="text-gray-600">Secure document upload, storage, and verification. Support for certificates, IDs, and academic records.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Schools Showcase Section --}}
    @if($schools->count() > 0)
    <div id="schools" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">Our Partner Schools</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Join one of our {{ $schools->count() }}+ partner institutions and start your journey towards academic excellence.</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($schools as $school)
                <div class="school-card bg-gray-50 rounded-xl p-6 flex flex-col items-center justify-center text-center border border-gray-100 hover:border-gray-200">
                    @if($school->logo)
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }}" class="h-12 w-auto mb-3 object-contain">
                    @else
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mb-3" style="background-color: {{ $school->primary_color ?? $primaryColor }}20;">
                            <span class="text-lg font-bold" style="color: {{ $school->primary_color ?? $primaryColor }}">
                                {{ substr($school->name, 0, 1) }}
                            </span>
                        </div>
                    @endif
                    <h4 class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $school->name }}</h4>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-10">
                <p class="text-gray-500 mb-4">And many more institutions joining soon!</p>
            </div>
        </div>
    </div>
    @endif

    {{-- How It Works Section --}}
    <div id="how-it-works" class="brand-gradient-light py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Get started with your application in four simple steps.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Register</h3>
                    <p class="text-gray-600">Create your account with your email and phone number to get started.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Apply</h3>
                    <p class="text-gray-600">Fill out the application form, select your program, and upload required documents.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <span class="text-3xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Pay</h3>
                    <p class="text-gray-600">Pay the application fee securely via M-PESA, card, or bank transfer.</p>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto brand-gradient rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <span class="text-3xl font-bold text-white">4</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3" style="color: {{ $primaryColor }}">Track</h3>
                    <p class="text-gray-600">Monitor your application status in real-time and receive updates.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Programs Section --}}
    <div id="programs" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">Available Programs</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Explore our diverse range of programs designed to prepare you for a successful career.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($programs as $program)
                <div class="group bg-gray-50 rounded-2xl p-6 border-2 border-gray-100 hover:border-gray-200 transition-all duration-300 hover:shadow-xl cursor-pointer">
                    <div class="w-12 h-12 brand-gradient rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-md">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                    </div>
                    <h3 class="text-base font-bold mb-2 group-hover:opacity-80 transition-colors" style="color: {{ $primaryColor }}">{{ $program->name }}</h3>
                    <div class="flex items-center space-x-2 mb-3">
                        <span class="px-2 py-1 text-xs rounded-full font-medium" style="background-color: {{ $primaryColor }}15; color: {{ $primaryColor }}">{{ ucfirst($program->level) }}</span>
                        <span class="px-2 py-1 bg-gray-200 text-gray-600 text-xs rounded-full">{{ $program->duration_years }} Years</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-4">{{ Str::limit($program->description ?? ($program->department->name ?? ''), 60) }}</p>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <span class="text-gray-400 text-xs">{{ $program->department->name ?? '' }}</span>
                        <a href="{{ route('login') }}" class="text-xs font-semibold flex items-center space-x-1 group/link" style="color: {{ $primaryColor }}">
                            <span>Apply</span>
                            <i class="fas fa-arrow-right text-xs group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-4 text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book-open text-gray-400 text-3xl"></i>
                    </div>
                    <p class="text-gray-500">Programs coming soon. Please check back later.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Testimonials Section --}}
    @if(count($testimonials) > 0)
    <div class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">What People Say</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Hear from students, parents, and administrators who have used our platform.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                <div class="testimonial-card bg-white rounded-2xl p-8 shadow-md border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 brand-gradient rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($testimonial['name'], 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-800">{{ $testimonial['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $testimonial['role'] }}</p>
                        </div>
                    </div>
                    <div class="flex mb-4">
                        @for($i = 0; $i < 5; $i++)
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @endfor
                    </div>
                    <p class="text-gray-600 italic">"{{ $testimonial['message'] }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Payment Section --}}
    <div id="payment" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: {{ $primaryColor }}">Secure Payment Options</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Multiple payment methods to make your application fee payment quick and secure.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-md border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-green-600">M</span>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">M-PESA</h3>
                    <p class="text-gray-600 text-sm">Pay via M-PESA mobile money. Instant confirmation.</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-md border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-blue-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Card Payment</h3>
                    <p class="text-gray-600 text-sm">Visa, Mastercard accepted. Secure online payment.</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-8 text-center shadow-md border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-purple-100 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-university text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-gray-800">Bank Transfer</h3>
                    <p class="text-gray-600 text-sm">Direct bank transfer. Details provided after application.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Section --}}
    <div class="brand-gradient py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Start Your Application Today</h2>
                    <p class="text-white/80 mb-8 text-lg">Join thousands of students who have successfully applied through our platform. Your future starts here!</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center space-x-2 bg-white px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all shadow-xl" style="color: {{ $primaryColor }}">
                            <span>Apply Now</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center justify-center space-x-2 border-2 border-white text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/10 transition-all">
                            <i class="fas fa-envelope"></i>
                            <span>Contact Us</span>
                        </a>
                    </div>
                </div>
                <div class="hidden md:flex justify-center">
                    <div class="relative">
                        <div class="w-64 h-64 bg-white/10 rounded-3xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <div class="w-48 h-48 bg-white/20 rounded-2xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-6xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact Section --}}
    <div id="contact" class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6" style="color: {{ $primaryColor }}">Get In Touch</h2>
                    <p class="text-gray-600 mb-8">Have questions? We're here to help. Reach out to us through any of the following channels.</p>
                    <div class="space-y-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 brand-gradient rounded-xl flex items-center justify-center">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Email</h4>
                                <a href="mailto:{{ $contactEmail }}" class="text-gray-600 hover:text-[{{ $primaryColor }}]">{{ $contactEmail }}</a>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 brand-gradient rounded-xl flex items-center justify-center">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">Phone</h4>
                                <a href="tel:{{ $contactPhone }}" class="text-gray-600 hover:text-[{{ $primaryColor }}]">{{ $contactPhone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-md border border-gray-100">
                    <h3 class="text-xl font-bold mb-4" style="color: {{ $primaryColor }}">Send us a message</h3>
                    <form id="contact-form" method="POST" action="{{ route('contact') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contact-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent focus:outline-none" style="--tw-ring-color: {{ $primaryColor }}" placeholder="Your name" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="contact-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent focus:outline-none" style="--tw-ring-color: {{ $primaryColor }}" placeholder="Your email" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea name="message" id="contact-message" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:border-transparent focus:outline-none" style="--tw-ring-color: {{ $primaryColor }}" placeholder="Your message" required></textarea>
                        </div>
                        <button type="submit" id="contact-submit" class="w-full btn-primary text-white px-6 py-3 rounded-lg font-medium">
                            <span id="contact-btn-text">Send Message</span>
                        </button>
                        <div id="contact-success" class="hidden p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"></div>
                        <div id="contact-error" class="hidden p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-gray-900 py-12 relative z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        @if($systemLogo)
                            <img src="{{ asset('storage/' . $systemLogo) }}" alt="{{ $systemName }}" class="h-8 w-auto">
                        @else
                            <div class="w-10 h-10 brand-gradient rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                        @endif
                        <span class="text-xl font-bold text-white">{{ $systemName }}</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $tagline }}</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#schools" class="hover:text-white transition-colors">Schools</a></li>
                        <li><a href="#programs" class="hover:text-white transition-colors">Programs</a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('super-admin.login') }}" class="hover:text-white transition-colors">System Login</a></li>
                        <li><a href="mailto:{{ $contactEmail }}" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="mailto:{{ $contactEmail }}" class="hover:text-white transition-colors">FAQs</a></li>
                        <li><a href="mailto:{{ $contactEmail }}" class="hover:text-white transition-colors">Report Issue</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Language</h4>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('lang', 'en') }}" class="{{ app()->getLocale() == 'en' ? 'text-white font-bold' : 'text-gray-400 hover:text-white' }} transition-colors text-sm">English</a>
                        <span class="text-gray-600">|</span>
                        <a href="{{ route('lang', 'sw') }}" class="{{ app()->getLocale() == 'sw' ? 'text-white font-bold' : 'text-gray-400 hover:text-white' }} transition-colors text-sm">Kiswahili</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    {!! $footerText !!}
                </p>
            </div>
        </div>
    </footer>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const icon = this.querySelector('i');
        menu.classList.toggle('hidden');
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.getElementById('mobile-menu').classList.add('hidden');
                document.querySelector('#mobile-menu-btn i').classList.remove('fa-times');
                document.querySelector('#mobile-menu-btn i').classList.add('fa-bars');
            }
        });
    });

    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('contact-submit');
        const btnText = document.getElementById('contact-btn-text');
        const successDiv = document.getElementById('contact-success');
        const errorDiv = document.getElementById('contact-error');

        submitBtn.disabled = true;
        btnText.textContent = 'Sending...';
        successDiv.classList.add('hidden');
        errorDiv.classList.add('hidden');

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successDiv.textContent = data.message || 'Message sent successfully!';
                successDiv.classList.remove('hidden');
                form.reset();
            } else {
                errorDiv.textContent = data.message || 'Something went wrong. Please try again.';
                errorDiv.classList.remove('hidden');
            }
        })
        .catch(() => {
            errorDiv.textContent = 'Something went wrong. Please try again.';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.disabled = false;
            btnText.textContent = 'Send Message';
        });
    });
</script>
@endpush
