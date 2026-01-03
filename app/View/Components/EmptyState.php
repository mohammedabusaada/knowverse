<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmptyState extends Component
{
    public function __construct(
        public string $title,
        public ?string $description = null
    ) {}

    public function render()
    {
        return view('components.empty-state');
    }
}

