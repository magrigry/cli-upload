<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Code extends Component
{
    public function __construct(
        public readonly string $language,
        private readonly bool $minify = false,
    ) {}

    public function renderCode(string $slot): string
    {
        if (! $this->minify) {
            return $slot;
        }

        $slot = str_replace("\n", ' ', $slot);

        return preg_replace('/\s+/', ' ', $slot);
    }

    public function render(): View
    {
        return view('components.code');
    }
}
