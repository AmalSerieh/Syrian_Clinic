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
            ->where(function ($query) {
                $query->whereNull('med_end_date')
                    ->orWhere('med_end_date', '>=', Carbon::now());
            })
            ->get();

        $this->info("✅ تم تحديث حالة $count دواء إلى غير نشط.");
        return Command::SUCCESS;
    }
}
