<?php

namespace App\Providers;

use App\Repositories\Api\PateintRecord\AllergyRepositoryInterface;
use App\Repositories\Api\PateintRecord\DiseaseRepositoryInterface;
use App\Repositories\Api\PateintRecord\MedicalAttachmentRepositoryInterface;
use App\Repositories\Api\PateintRecord\MedicalFileRepositoryInterface;
use App\Repositories\Api\PateintRecord\MedicationRepositoryInterface;
use App\Repositories\Api\PateintRecord\OperationRepositoryInterface;
use App\Repositories\Api\PateintRecord\PatientProfileRepositoryInterface;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Auth\OtpRepositoryInterface;
use App\Repositories\Eloquent\Api\PateintRecord\AllergyRepository;
use App\Repositories\Eloquent\Api\PateintRecord\DiseaseRepository;
use App\Repositories\Eloquent\Api\PateintRecord\MedicalAttachmentRepository;
use App\Repositories\Eloquent\Api\PateintRecord\MedicalFileRepository;
use App\Repositories\Eloquent\Api\PateintRecord\MedicationRepository;
use App\Repositories\Eloquent\Api\PateintRecord\OperationRepository;
use App\Repositories\Eloquent\Api\PateintRecord\PatientProfileRepository;
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
        $this->app->bind(PatientProfileRepositoryInterface::class, PatientProfileRepository::class);
        $this->app->bind(AllergyRepositoryInterface::class, AllergyRepository::class);
        $this->app->bind(MedicalFileRepositoryInterface::class, MedicalFileRepository::class);
        $this->app->bind(MedicalAttachmentRepositoryInterface::class, MedicalAttachmentRepository::class);
        $this->app->bind(DiseaseRepositoryInterface::class, DiseaseRepository::class);
        $this->app->bind(MedicationRepositoryInterface::class, MedicationRepository::class);
        $this->app->bind(OperationRepositoryInterface::class, OperationRepository::class);



    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
