<?php

namespace App\View\Components\Partials\Custom;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datatable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public String $idTable, public bool $serverSide)
    {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.partials.custom.datatable');
    }
}
