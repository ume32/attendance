<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Error extends Component
{
    public $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    public function render()
    {
        return view('components.error');
    }
}
