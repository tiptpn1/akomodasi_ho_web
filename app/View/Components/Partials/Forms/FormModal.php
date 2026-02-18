<?php

namespace App\View\Components\Partials\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormModal extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public String $modalId, public String $modalTitle, public String $formMethod, public String $formUrl)
    {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.partials.forms.form-modal');
    }
}
