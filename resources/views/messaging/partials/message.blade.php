@php
$isOwn = $message->sender_id === auth()->id();
$alignment = $isOwn ? 'justify-end' : 'justify-start';
$bgColor = $isOwn ? 'bg-purple-600 text-white' : 'bg-white text-gray-900 border border-gray-200';
$roundedClass = $isOwn ? 'rounded-2xl rounded-br-sm' : 'rounded-2xl rounded-bl-sm';
@endphp

<div class="flex {{ $alignment }}" data-message-id="{{ $message->id }}">
    <div class="max-w-[70%] {{ $bgColor }} {{ $roundedClass }} p-3 shadow-sm">
        @if(!$isOwn)
            <p class="text-xs font-semibold text-purple-600 mb-1">{{ $message->sender->fullName() }}</p>
        @endif
        
        @if($message->content)
            <p class="text-sm whitespace-pre-wrap">{{ $message->content }}</p>
        @endif
        
        @if($message->hasAttachment())
            @if($message->is_image)
                <a href="{{ $message->attachment_url }}" target="_blank">
                    <img src="{{ $message->attachment_url }}" alt="{{ $message->attachment_name }}" 
                         class="max-w-[250px] rounded-lg mt-2">
                </a>
            @else
                <a href="{{ $message->attachment_url }}" download="{{ $message->attachment_name }}" 
                   class="flex items-center p-2 {{ $isOwn ? 'bg-white/10 hover:bg-white/20' : 'bg-gray-100 hover:bg-gray-200' }} rounded-lg mt-2 transition-colors">
                    <i class="fas fa-file {{ $isOwn ? 'text-white/80' : 'text-gray-500' }} mr-2"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium truncate {{ $isOwn ? 'text-white' : 'text-gray-700' }}">{{ $message->attachment_name }}</p>
                        <p class="text-xs {{ $isOwn ? 'text-white/70' : 'text-gray-500' }}">{{ $message->formatted_size }}</p>
                    </div>
                    <i class="fas fa-download ml-2 {{ $isOwn ? 'text-white/80' : 'text-gray-500' }}"></i>
                </a>
            @endif
        @endif
        
        <div class="flex items-center justify-end mt-1 space-x-1 {{ $isOwn ? 'text-white/70' : 'text-gray-400' }}">
            <span class="text-xs">{{ $message->created_at->diffForHumans() }}</span>
            @if($message->is_edited)
                <span class="text-xs">(edited)</span>
            @endif
            @if($isOwn)
                @if($message->read_at)
                    <i class="fas fa-check-double text-xs"></i>
                @else
                    <i class="fas fa-check text-xs"></i>
                @endif
            @endif
        </div>
    </div>
</div>
