<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTestRequest;
use App\Http\Resources\TestsResource;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{
    //عرض جميع العمليات ✅
    public function index()
    {
        $user = Auth::user();
        $record = $user->patient->patient_record;
        if (!$record) {
            return response()->json(['message' => trans('message.no_record')]);
        }
        $test = $record->tests;
        if ($test->isEmpty()) {
            return response()->json(['message' => trans('message.not_filled_yet')]);
        }
        $this->authorize('viewAny', [Test::class, $record]);
        return TestsResource::collection($test);
    }

    //عرض عملية واحد ✅
    public function show(Test $test)
    {
        $this->authorize('view', $test);
        return new TestsResource($test);
    }

    //إنشاء عملية ✅
    public function store(StoreTestRequest $request)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        if ($record->tests_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('create', [Test::class, $record]);

        // التحقق من عدم وجود فحص بنفس الاسم والتاريخ مسبقًا
        $exists = $record->tests()
            ->where('test_name', $request->test_name)
            ->where('test_date', $request->test_date)
            ->exists();

        if ($exists) {
            return response()->json(['message' => trans('message.test_already_exists')], 422);
        }

        // التعامل مع test_result سواء كان ملفًا أو نصًا
        $data = $request->validated();

        if ($request->hasFile('test_result')) {
            $path = $request->file('test_result')->store('test_results', 'public');
            $data['test_result'] = $path;
        }

        $test = $record->tests()->create($data);

        return new TestsResource($test);
    }


    //✅ تأكيد الإرسال
    public function submit()
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        if (!$record) {
            return response()->json(['message' => trans('message.no_record')], 404);
        }

        $this->authorize('confirmSubmission', [Test::class, $record]);

        // التحقق من وجود أدوية
        if ($record->tests->isEmpty()) {
            return response()->json(['message' => trans('message.no_tests_found')], 404);
        }

        if ($record->tests_submitted) {
            return response()->json(['message' => trans('message.test_already_submitted')], 400);
        }

        $record->update(['tests_submitted' => 1]);

        if ($record->tests_submitted == 1) {
            return response()->json(['message' => trans('message.test_submitted_success')]);
        } else {
            return response()->json(['message' => trans('message.test_submitted_failed')], 500);
        }
    }

    // ✅ تعديل فحص للطبيب

    public function update(StoreTestRequest $request, Test $test)
    {
        $user = auth()->user();
        $record = $user->patient->patient_record;

        // التأكد أن الفحص مرتبط بسجل المريض نفسه
        if (!$record || $test->patient_record_id !== $record->id) {
            return response()->json(['message' => trans('message.unauthorized')], 403);
        }

        if ($record->tests_submitted) {
            return response()->json(['message' => trans('message.submitted_already')], 403);
        }

        $this->authorize('update', $test);

        $data = $request->validated();

        if ($request->hasFile('test_result')) {
            // حذف الملف القديم إذا كان موجودًا
            if ($test->test_result && Storage::disk('public')->exists($test->test_result)) {
                Storage::disk('public')->delete($test->test_result);
            }

            // حفظ الملف الجديد
            $path = $request->file('test_result')->store('test_results', 'public');
            $data['test_result'] = $path;
        } else {
            $data['test_result'] = $request->input('test_result');
        }

        $test->update($data);

        return new TestsResource($test);
    }


    // ✅ حذف فحص للطبيب
    public function destroy(Test $test)
    {
        $this->authorize('delete', $test);
        $test->delete();

        return response()->json(['message' => trans('message.test_deleted')]);
    }
}
