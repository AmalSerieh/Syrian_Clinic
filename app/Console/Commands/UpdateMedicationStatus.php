<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medication;
use Carbon\Carbon;

class UpdateMedicationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medications:update-medication-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'تحديث حالة is_active للأدوية المؤقتة التي انتهى تاريخها';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // فقط الأدوية المؤقتة current
        $count = Medication::where('med_type', 'current')
            ->whereNotNull('med_end_date')
            ->where('med_end_date', '<', $now)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $this->info("✅ تم تحديث حالة $count دواء إلى غير نشط.");
        return Command::SUCCESS;
    }
}
