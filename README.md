# TALLbot

## About TALLbot

TALLbot is a streaming bot built for PHP devs.

**Technology Stack:**
- [TALL stack](https://talltips.novate.co.uk/)
- [FilamentPHP](https://filamentphp.com/)
- [AMPHP](https://amphp.org/)
- [NativePHP Desktop](https://nativephp.com/)

## Development Setup

1. Clone the repo
2. Install dependencies
    ```shell
    composer install && npm install
    ```
3. Copy `.env.example` to `.env` and fill in _TWITCH_CLIENT_ID_ and _TWITCH_CLIENT_SECRET_.
4. Set the Laravel security key
    ```shell
    php artisan key:generate
    ```

## Development Run

1. Run the bot
    ```shell
    composer native:dev 
    ```
