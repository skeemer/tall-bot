<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class Chat extends Page
{
    protected string $view = 'filament.pages.chat';

    public function getHeader(): ?View
    {
        return view('blank');
    }
}
