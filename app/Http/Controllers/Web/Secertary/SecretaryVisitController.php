<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\Request;

class SecretaryVisitController extends Controller
{
    //
    public function pendingPayments()
    {
        $visits = Visit::where('v_status', 'completed')
            ->where('v_paid', false)
            ->whereNotNull('v_price')
            ->with(['patient', 'doctor'])
            ->get();

        return view('secretary.visits.pending_payments', compact('visits'));
    }
    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
        ]);

        $visit = Visit::findOrFail($id);

        $visit->update([
            'v_paid' => true,
            'v_payment_method' => $request->payment_method,
        ]);

        return back()->with('success', 'تم تأكيد الدفع.');
    }

    public function paymentReport(Request $request)
    {
        $query = Visit::where('v_paid', true)
            ->when($request->filled('from'), fn($q) => $q->whereDate('updated_at', '>=', $request->from))
            ->when($request->filled('to'), fn($q) => $q->whereDate('updated_at', '<=', $request->to))
            ->with(['doctor', 'patient']);

        $visits = $query->get();

        $total = $visits->sum('v_price');

        return view('secretary.reports.payments', compact('visits', 'total'));
    }

}
