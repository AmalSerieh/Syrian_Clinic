<?php

namespace App\Providers;

use App\Models\Allergy;
use App\Models\Disease;
use App\Models\FamilyHistory;
use App\Models\MedicalFile;
use App\Models\Medication;
use App\Models\Operation;
use App\Models\Patient_profile;
use App\Models\Patient_record;
use App\Models\Test;
use App\Policies\AllergiesPolicy;
use App\Policies\DiseasesPolicy;
use App\Policies\FamilyHistoriesPolicy;
use App\Policies\MedicalFilesPolicy;
use App\Policies\MedicationsPolicy;
use App\Policies\OperationsPolicy;
use App\Policies\PatientProfilePolicy;
use App\Policies\PatientRecordPolicy;
use App\Policies\TestsPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */

    protected $policies = [
        Operation::class => OperationsPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Patient_record::class, PatientRecordPolicy::class);
        Gate::policy(Patient_profile::class, PatientProfilePolicy::class);
        Gate::policy(Disease::class, DiseasesPolicy::class);
        Gate::policy(Medication::class, MedicationsPolicy::class);
        Gate::policy(Operation::class, OperationsPolicy::class);
        Gate::policy(Test::class, TestsPolicy::class);
        Gate::policy(Allergy::class, AllergiesPolicy::class);
        Gate::policy(FamilyHistory::class, FamilyHistoriesPolicy::class);
        Gate::policy(MedicalFile::class, MedicalFilesPolicy::class);
    }

}
