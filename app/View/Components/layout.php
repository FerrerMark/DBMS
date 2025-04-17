<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Layout extends Component
{
    public $showHeader;
    public $showFooter;

    public function __construct($showHeader = true, $showFooter = true)
    {
        $this->showHeader = $showHeader;
        $this->showFooter = $showFooter;
    }

    public function render()
    {
        return view('components.layout');
    }
}
