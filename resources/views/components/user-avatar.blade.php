@php
    $avatarClass = $class ?? '';
@endphp
@if($user && $user->photo)
    <img src="{{ asset('storage/' . $user->photo) }}" 
         alt="Avatar" 
         class="rounded-full object-cover {{ $avatarClass }}"
         style="width: {{ $size }}px; height: {{ $size }}px;">
@elseif($user)
    <img src="{{ $user->avatar_url }}" 
         alt="Avatar"
         class="rounded-full object-cover {{ $avatarClass }}"
         style="width: {{ $size }}px; height: {{ $size }}px;">
@elseif($url ?? null)
    <img src="{{ $url }}" 
         alt="Avatar"
         class="rounded-full object-cover {{ $avatarClass }}"
         style="width: {{ $size }}px; height: {{ $size }}px;">
@else
    <img src="https://ui-avatars.com/api/?name={{ urlencode($initials ?? 'U') }}&color=7C3AED&background=F3E8FF&size={{ $size }}"
         alt="Avatar"
         class="rounded-full object-cover {{ $avatarClass }}"
         style="width: {{ $size }}px; height: {{ $size }}px;">
@endif
