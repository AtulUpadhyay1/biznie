<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SubmitBtn extends Component
{
    /**
     * Create a new component instance.
     */
    public $text = "";

    /**
     * Optional Livewire method. When given, the button calls it with wire:click
     * instead of submitting — needed for actions that sit outside a <form>,
     * such as the quick-add modals on the commodity-product screen.
     */
    public $function = "";

    public function __construct($text, $function = "")
    {
        $this->text = $text;
        $this->function = $function;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.submit-btn');
    }
}
