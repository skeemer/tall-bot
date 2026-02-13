<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('twitch_connections', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->nullable();
            $table->string('access_token');
            $table->string('refresh_token');
            $table->dateTime('expires_at');
            $table->text('scope')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('twitch_connections');
    }
};
