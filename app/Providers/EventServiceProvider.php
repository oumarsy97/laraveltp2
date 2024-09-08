<?php

namespace App\Providers;

use App\Events\ImageUploaded;
use App\Events\LoyaltyCardRequested;
use App\Jobs\StoreImageInCloud as JobsStoreImageInCloud;
use App\Listeners\GenerateLoyaltyCard;
use App\Listeners\HandleImageUploadListener;
use App\Listeners\SendMail;
use App\Listeners\StoreImageInCloud;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ImageUploaded::class => [
            HandleImageUploadListener::class,
        ],
        LoyaltyCardRequested::class => [
            GenerateLoyaltyCard::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //

        //  Event::listen(ImageUploaded::class, JobsStoreImageInCloud::class);
        Event::listen(
            ImageUploaded::class,
            [HandleImageUploadListener::class, 'handle']
        );
        // Event::listen(
        //     ImageUploaded::class,
        //     [SendMail::class, 'handle']
        // );

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }


}
