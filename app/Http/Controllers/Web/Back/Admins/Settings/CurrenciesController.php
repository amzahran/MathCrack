<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Currency;

class CurrenciesController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show settings')) {
            return view('themes/default/back.permission-denied');
        }

        $currencies = Currency::all();

        return view('themes/default/back.admins.settings.currencies.currencies-list', ['currencies' => $currencies]);
    }

    public function exchange(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        if(!\updateCurrencyRates()) {
            return redirect()->back()->with('error', __('l.Failed to update currency rates'));
        }

        return redirect()->back()->with('success', __('l.Currency rates updated successfully'));
    }

    public function status(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $currency = Currency::findOrFail(decrypt($request->id));

        // تحديث العملة
        $currency->is_active = !$currency->is_active;
        $currency->save();

        return redirect()->back()->with('success', __('l.Currency updated successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $currency = Currency::findOrFail(decrypt($request->id));

        return view('themes/default/back.admins.settings.currencies.currencies-edit', ['currency' => $currency]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $currency = Currency::findOrFail(decrypt($request->id));

        $currency->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'rate' => $request->rate ?? $currency->rate,
            'is_manual' => $request->boolean('is_manual'),
        ]);

        return redirect()->back()->with('success', __('l.Currency updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $currency = Currency::findOrFail(decrypt($request->id));

        $currency->delete();

        return redirect()->back()->with('success', __('l.Currency deleted successfully.'));
    }
}
