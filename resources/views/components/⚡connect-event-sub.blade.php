<?php

use App\Console\Commands\RunEventSubClient;
use App\Models\TwitchConnection;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?TwitchConnection $connection = null;

    public bool $enabled;

    public bool $errorState = false;

    public function mount(): void
    {
        $this->enabled = (bool) ChildProcess::get('eventsub');
    }

    public function updatingEnabled(bool $value): void
    {
        if ($value) {
            ChildProcess::artisan('app:run-event-sub-client', 'eventsub', persistent: true);
        } else {
            ChildProcess::stop('eventsub');
        }
    }

    #[On('native:' . Native\Desktop\Events\ChildProcess\ProcessSpawned::class)]
    public function connectionStarted(string $alias): void
    {
        if ($alias !== 'eventsub') {
            return;
        }

        if (! $this->enabled) {
            $this->enabled = true;
        } elseif ($this->errorState) {
            $this->errorState = false;
        } else {
            $this->skipRender();
        }
    }

    #[On('native:'.Native\Desktop\Events\ChildProcess\ProcessExited::class)]
    public function connectionStopped(string $alias, int $code): void
    {
        if ($alias !== 'eventsub') {
            return;
        }

        if (! $this->enabled) {
            $this->skipRender();
        } elseif ($code === 1) {
            $this->errorState = true;
        } else {
            $this->enabled = false;
        }
    }
};
?>

<div>
    <flux:field variant="inline">
        <flux:switch :class="'live'.($errorState ? ' error' : '')" :disabled="$errorState" wire:model.live="enabled"/>
        <flux:label>Bot Online</flux:label>
    </flux:field>
</div>
