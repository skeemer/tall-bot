<?php

namespace App\Filament\Pages;

use App\Models\TwitchConnection;
use App\Settings\TwitchManagement;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\On;
use Native\Desktop\Events\ChildProcess\ProcessExited;
use Native\Desktop\Events\ChildProcess\ProcessSpawned;
use Native\Desktop\Events\Windows\WindowClosed;
use Native\Desktop\Facades\ChildProcess;
use Native\Desktop\Facades\Window;

class ManageConnections extends SettingsPage implements HasTable
{
    use InteractsWithTable;

    protected $listeners = [
        'native:'.WindowClosed::class => '$refresh',
    ];

    protected static string|null|\UnitEnum $navigationGroup = 'Settings';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string $settings = TwitchManagement::class;

    protected string $view = 'filament.pages.manage-connections';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('botConnection')
                    ->options(TwitchConnection::orderBy('display_name')->pluck('display_name', 'id'))
                    ->disabled(fn () => ChildProcess::get('eventsub') !== null)
                    ->live()
                    ->afterStateUpdated(fn () => $this->save()),
                Select::make('chatConnection')
                    ->options(TwitchConnection::orderBy('display_name')->pluck('display_name', 'id'))
                    ->live()
                    ->afterStateUpdated(fn () => $this->save()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TwitchConnection::query())
            ->columns([
                TextColumn::make('twitch_user_id')
                    ->label('Twitch ID'),
                TextColumn::make('display_name')
                    ->label('Name'),
                IconColumn::make('is_bot_connection')
                    ->label('Bot')
                    ->boolean(),
                IconColumn::make('is_chat_connection')
                    ->label('Chat')
                    ->boolean(),
            ])
            ->heading('Connections')
            ->headerActions([
                Action::make('connect')
                    ->color('primary')
                    ->action(function () {
                        Window::open('oauth')
                            ->width(600)
                            ->height(800)
                            ->url(route('twitch.auth.redirect'));
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->disabled(fn (TwitchConnection $record): bool => $record->is_bot_connection || $record->is_chat_connection),
            ]);
    }

    #[On('native:'.ProcessExited::class),On('native:'.ProcessSpawned::class)]
    public function trackEventSubRunning(string $alias): void
    {
        // $parsedEvent = json_decode($event);
        if ($alias !== 'eventsub') {
            $this->skipRender();
        }
    }
}
