<?php

namespace App\Providers;

use App\Models\Allergy;
use App\Models\Disease;
use App\Models\FamilyHistory;
use App\Models\MedicalAttachment;
use App\Models\MedicalFile;
use App\Models\Medication;
use App\Models\MedicationAlarm;
use App\Models\Operation;
use App\Models\Patient_profile;
use App\Models\Patient_record;
use App\Models\Test;
use App\Policies\AllergiesPolicy;
use App\Policies\Api\PatientRecord\AllergyPolicy;
use App\Policies\Api\PatientRecord\DiseasePolicy;
use App\Policies\Api\PatientRecord\MedicalAttachmentPolicy;
use App\Policies\Api\PatientRecord\MedicalFilePolicy;
use App\Policies\Api\PatientRecord\MedicationAlarmPolicy;
use App\Policies\Api\PatientRecord\MedicationPolicy;
use App\Policies\Api\PatientRecord\OperationPolicy;
use App\Policies\DiseasesPolicy;
use App\Policies\FamilyHistoriesPolicy;
use App\Policies\MedicalFilesPolicy;
use App\Policies\MedicationsPolicy;
use App\Policies\OperationsPolicy;
use \App\Policies\Api\PatientRecord\PatientProfilePolicy;
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
        Patient_profile::class => PatientProfilePolicy::class,
        Allergy::class => AllergyPolicy::class,
        MedicalFile::class => MedicalFilePolicy::class,
        MedicalAttachment::class => MedicalAttachmentPolicy::class,
        Disease::class => DiseasePolicy::class,
        Medication::class => MedicationPolicy::class,
        Operation::class => OperationPolicy::class,
        MedicationAlarm::class => MedicationAlarmPolicy::class,

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
        Gate::policy(Allergy::class, AllergyPolicy::class);
        Gate::policy(MedicalFile::class, MedicalFilePolicy::class);
        Gate::policy(MedicalAttachment::class, MedicalAttachmentPolicy::class);
        Gate::policy(Disease::class, DiseasePolicy::class);
        Gate::policy(Medication::class, MedicationPolicy::class);
        Gate::policy(Operation::class, OperationPolicy::class);
        Gate::policy(MedicationAlarm::class, MedicationAlarmPolicy::class);

    }

}
