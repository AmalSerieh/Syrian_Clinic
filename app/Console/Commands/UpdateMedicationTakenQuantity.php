<?php

namespace App\Console\Commands;

use App\Models\Medication;
use App\Services\Api\PateintRecord\MedicationService;
use Illuminate\Console\Command;

class UpdateMedicationTakenQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medications:update-progress';

    /**
     * The console command description.
     *
     * @var string
     */
      protected $description = 'تحديث كمية med_taken_quantity لكل الأدوية بناءً على التكرار والتاريخ';
      public function __construct(protected MedicationService $service)
    {
        parent::__construct();
    }
     public function handle(): int
    {
        $this->info('🔄 جاري تحديث الكميات...');
        $this->service->updateAllMedications();
        $this->info('✅ تم التحديث بنجاح!');
        return Command::SUCCESS;
    }

    /**
     * Execute the console command.
     */
    /* public function handle()
    {
        $this->info('جارٍ تحديث كميات taken...');
        $medications = Medication::where('is_active', true)->get();

    foreach ($medications as $med) {
        $takenTillNow = app(MedicationService::class)->calculateTakenTillNow($med->toArray());
        $med->update(['med_total_quantity' => $takenTillNow]);
    }

        Medication::chunk(100, function ($medications) {
            foreach ($medications as $med) {
                $med->med_taken_quantity = $med->calculateTakenQuantity();
                $med->save();
            }
        });

        $this->info('✅ تم تحديث جميع الكميات بنجاح!');
    } */
}
