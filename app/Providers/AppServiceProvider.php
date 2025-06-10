<?php

namespace App\Providers;

use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\OtpRepositoryInterface;
use App\Repositories\Eloquent\OtpRepository;
use App\Repositories\Eloquent\Profile\FileStorage;
use App\Repositories\Eloquent\Profile\FileStorageRepository;
use App\Repositories\Eloquent\Profile\PatientRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\AuthRepository;
use App\Repositories\Auth\UserRepositoryInterface;
use App\Repositories\Profile\FileStorageInterface;
use App\Repositories\Profile\PatientRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(OtpRepositoryInterface::class, OtpRepository::class);
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
        $this->app->bind(FileStorageInterface::class, FileStorageRepository::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
