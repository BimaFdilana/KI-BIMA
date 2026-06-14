<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CountdownTimer extends Component
{
    public $seconds;
    public $event;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($seconds = 120, $event = 'countdown-finished')
    {
        $this->seconds = $seconds;
        $this->event = $event;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.countdown-timer');
    }
}
