<?php

use Livewire\Component;

new class extends Component
{
    public function getUrl(): string
    {
        return 'https://id.twitch.tv/oauth2/authorize?'.http_build_query([
                'response_type' => 'code',
                'client_id' => config('services.twitch.client_id'),
                'scope' => 'channel:manage:broadcast user:read:chat user:bot channel:bot user:write:chat',
                'redirect_uri' => str_replace('127.0.0.1', 'localhost', route('twitch.auth.callback')),
            ]);
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <flux:button :href="$this->getUrl()">Link Twitch</flux:button>
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
        <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
    <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
        <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
    </div>
</div>
