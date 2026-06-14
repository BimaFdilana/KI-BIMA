<?php

namespace App\View\Components;

use App\Models\Auth\UserModel;
use Illuminate\View\Component;

class UserAvatar extends Component
{
    public $user;
    public $size;

    /**
     * Create a new component instance.
     *
     * @param UserModel $user
     * @param int $size
     * @return void
     */
    public function __construct(UserModel $user, $size = 10)
    {
        $this->user = $user;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.user-avatar');
    }
}
