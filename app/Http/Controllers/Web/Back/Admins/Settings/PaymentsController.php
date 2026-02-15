<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PaymentGatewaySetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $payments = PaymentGateway::with('settings')->get();

        return view('themes/default/back.admins.settings.payments', ['payments' => $payments]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $request->validate([
            'status' => 'required|in:0,1',
            'fees' => 'required|max:10',
            'fees_type' => 'required|in:percentage,fixed',
            'description' => 'required|string|max:255',
        ]);

        $gateway = PaymentGateway::findOrFail($id);
        $defaultLanguage = Setting::where('option', 'default_language')->first()->value;

        // تحويل الوصف الحالي إلى مصفوفة إذا كان نصاً
        $currentDescription = is_array($gateway->description) ? $gateway->description : [];

        $gateway->update([
            'fees' => $request->fees,
            'fees_type' => $request->fees_type,
            'status' => $request->status,
            'description' => array_merge($currentDescription, [$defaultLanguage => $request->description]),
        ]);

        $envSettings = [];

        foreach ($request->except(['_token', 'id', 'status', 'image', 'name', 'description', 'fees', 'fees_type']) as $key => $value) {
            PaymentGatewaySetting::updateOrCreate(
                ['gateway_id' => $gateway->id, 'key' => $key],
                ['value' => $value]
            );
            $envSettings[strtoupper($key)] = $value;
        }

        // مسح الكاش وتحديثه
        Cache::forget('app_cached_data');

        // تحديث ملف .env
        update_env($envSettings);

        return redirect()->back()->with('success', __('l.Payment Gateway updated successfully.'));
    }

    public function translate(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $gateway = PaymentGateway::findOrFail($id);

        // تحويل الوصف الحالي إلى مصفوفة إذا كان نصاً
        $currentDescription = is_array($gateway->description) ? $gateway->description : [];

        $translations = [];
        foreach ($request->except(['_token', 'id']) as $key => $value) {
            $languageCode = str_replace('description-', '', $key);
            $translations[$languageCode] = $value;
        }

        $gateway->update([
            'description' => array_merge($currentDescription, $translations),
        ]);

        return redirect()->back()->with('success', __('l.Payment Gateway translated successfully.'));
    }
}
