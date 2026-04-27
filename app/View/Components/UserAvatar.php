<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;

class UserAvatar extends Component
{
    public ?User $user;
    public int $size;
    public string $class;

    public function __construct(
        ?User $user = null,
        int $size = 40,
        string $class = ''
    ) {
        $this->user = $user;
        $this->size = $size;
        $this->class = $class;
    }

    public function render()
    {
        return view('components.user-avatar');
    }
}
