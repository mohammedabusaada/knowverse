<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FollowButton extends Component
{
    public bool $followed;

    public function __construct(bool $followed = false)
    {
        $this->followed = $followed;
    }

    public function render()
    {
        return view('components.follow-button');
    }
}
