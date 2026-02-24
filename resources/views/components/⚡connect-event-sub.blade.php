<?php

use App\Console\Commands\RunEventSubClient;
use App\Events\BotConnectionSettingChanged;
use App\Events\SubscriptionSuccess;
use App\Models\TwitchConnection;
use App\Settings\TwitchManagement;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Forms\Components;
use Filament\Schemas\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component implements HasSchemas {
    use InteractsWithSchemas;

    protected $listeners = [
        'native:'.BotConnectionSettingChanged::class => '$refresh',
    ];

    public ?TwitchConnection $connection = null;

    public bool $enabled;

    public bool $errorState = false;

    public bool $live = false;

    public bool $quittingAt = false;

    public function mount(): void
    {
        $this->enabled = (bool) ChildProcess::get('eventsub');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Toggle::make('enabled')
                    ->label('Bot Online')
                    ->onColor(fn () => $this->errorState ? 'danger' : (!$this->live ? 'warning' : 'success'))
                    ->disabled(fn () => app(TwitchManagement::class)->botConnection === null)
                    ->live(),
            ]);
    }

    public function updatingEnabled(bool $value): void
    {
        if ($value) {
            ChildProcess::artisan('app:run-event-sub-client', 'eventsub', persistent: true);
        } else {
            if (ChildProcess::get('eventsub')) {
                ChildProcess::stop('eventsub');
            } else {
                $quittingAt = true;
            }
        }
    }

    #[On('native:' . Native\Desktop\Events\ChildProcess\ProcessSpawned::class)]
    public function connectionStarted(string $alias): void
    {
        if ($alias !== 'eventsub') {
            return;
        }

        if ($this->quittingAt) {
            ChildProcess::stop('eventsub');
            $this->quittingAt = false;
        } elseif (!$this->enabled) {
            $this->enabled = true;
        } elseif ($this->errorState) {
            $this->errorState = false;
        } else {
            $this->skipRender();
        }
    }

    #[On('native:' . Native\Desktop\Events\ChildProcess\ProcessExited::class)]
    public function connectionStopped(string $alias, int $code): void
    {
        if ($alias !== 'eventsub') {
            return;
        }

        $this->live = false;

        if (!$this->enabled) {
            $this->skipRender();
        } elseif ($code === 1) {
            $this->errorState = true;
        } else {
            $this->enabled = false;
        }
    }

    #[On('native:'.SubscriptionSuccess::class)]
    public function subscriptionSuccess(): void
    {
        $this->errorState = false;
        $this->live = true;
    }
};
?>

<form class="fi-sidebar-nav shrink-1 grow-0 pb-2">
    {{ $this->form }}
</form>
