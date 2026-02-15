<?php

namespace App\Http\Controllers\Web\Back\Admins\Subscribers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class SubscribersController extends Controller
{
    public function index()
    {
        if (!Gate::allows('show newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        if (request()->ajax()) {
            $subscribers = Subscriber::query();

            return DataTables::of($subscribers)
                ->addIndexColumn()
                ->addColumn('action', function ($subscriber) {
                    return '
                        <a href="' . route('dashboard.admins.subscribers-edit', ['id' => encrypt($subscriber->id)]) . '"
                            class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="' . __('l.Edit') . '">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm delete-subscriber"
                            data-id="' . encrypt($subscriber->id) . '" data-bs-toggle="tooltip" title="' . __('l.Delete') . '">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->editColumn('email', function ($subscriber) {
                    return Str::limit($subscriber->email, 75);
                })
                ->editColumn('created_at', function ($subscriber) {
                    return Carbon::parse($subscriber->created_at)->format('Y-m-d');
                })
                ->editColumn('is_active', function ($subscriber) {
                    return $subscriber->is_active ? '<span class="badge bg-label-success">' . __('l.Active') . '</span>' : '<span class="badge bg-label-danger">' . __('l.Inactive') . '</span>';
                })
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        return view('themes.default.back.admins.subscribers.subscribers-list');
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = new Subscriber();
        $subscriber->email = $request->input('email');
        $subscriber->is_active = 1;
        $subscriber->unsubscribe_token = Str::random(32);
        $subscriber->save();

        return redirect()->back()->with('success', __('l.Subscriber added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $subscriber = Subscriber::findOrFail($id);

        return view('themes.default.back.admins.subscribers.subscribers-edit', ['subscriber' => $subscriber]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $request->validate([
            'email' => 'required|email|unique:subscribers,email,' . $id,
            'is_active' => 'required|boolean',
        ]);

        $subscriber = Subscriber::findOrFail($id);

        $subscriber->email = $request->input('email');
        $subscriber->is_active = $request->boolean('is_active');
        $subscriber->save();

        return redirect()->back()->with('success', __('l.Subscriber updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $subscriber = Subscriber::findOrFail($id);

        $subscriber->delete();

        return redirect()->back()->with('success', __('l.Subscriber deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete newsletters_subscribers')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        Subscriber::whereIn('id', $ids)->delete();
        return redirect()->back()->with('success', __('l.Subscribers deleted successfully'));
    }

}
