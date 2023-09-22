<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AddBtn extends Component
{
    /**
     * Create a new component instance.
     */
    public $text = "";
    public $function = "";

    public function __construct($text, $function)
    {
        $this->text = $text;
        $this->function = $function;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.add-btn');
    }
}
