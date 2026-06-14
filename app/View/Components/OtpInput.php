<?php

namespace App\View\Components;

use Illuminate\View\Component;

class OtpInput extends Component
{
    public $name;
    public $length;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name = 'otp', $length = 5)
    {
        $this->name = $name;
        $this->length = $length;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.otp-input');
    }
}
