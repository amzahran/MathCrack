<?php

namespace App\Http\Controllers\Web\Back\Admins\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlockedIp;
use Illuminate\Support\Facades\Gate;

class FirewallsController extends Controller
{
    public function store(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $ip = new BlockedIp();
        $ip->ip = $request->input('ip');
        $ip->save();

        return redirect()->back()->with('success', __('l.blocked IP added successfully'));

    }

    public function delete(Request $request)
    {
        if (!Gate::allows('edit settings')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId=$request->id;
        $id=decrypt($encryptedId);

        $ip = BlockedIp::findOrFail($id);

        $ip->delete();

        return redirect()->back()->with('success', __('l.blocked IP deleted successfully'));
    }

}