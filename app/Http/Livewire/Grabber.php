<?php

namespace App\Http\Livewire;

use App\Http\Controllers\WikiController;
use App\Models\Person;
use Livewire\Component;

class Grabber extends Component
{
    public $check;
    public $error;
    public $url;
    public $success;

    public function render()
    {
        return view('livewire.grabber');
    }

    public function startGrabbing()
    {
        $this->success = null;
        $this->success = (new WikiController)->collect($this->url);
    }
}
