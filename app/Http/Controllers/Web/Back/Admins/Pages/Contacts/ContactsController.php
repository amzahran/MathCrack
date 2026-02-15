<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages\Contacts;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ContactsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show contact_us')) {
            return view('themes.default.back.permission-denied');
        }

        if ($request->ajax()) {
            $contacts = Contact::query();

            return DataTables::of($contacts)
                ->addIndexColumn()
                ->addColumn('name', function ($contact) {
                    return $contact->name;
                })
                ->addColumn('email', function ($contact) {
                    return $contact->email;
                })
                ->addColumn('phone', function ($contact) {
                    return $contact->phone;
                })
                ->addColumn('subject', function ($contact) {
                    return $contact->subject;
                })
                ->addColumn('details', function ($contact) {
                    return Str::limit($contact->details, 50);
                })
                ->addColumn('status', function ($contact) {
                    if ($contact->status == '2') {
                        return '<span class="badge bg-success">' . __('l.Contacted') . '</span>';
                    } elseif ($contact->status == '1') {
                        return '<span class="badge bg-info">' . __('l.Read') . '</span>';
                    } else {
                        return '<span class="badge bg-danger blink">' . __('l.Unread') . '</span>';
                    }
                })
                ->addColumn('created_at', function ($contact) {
                    return $contact->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($contact) {
                    $id = encrypt($contact->id);
                    $actions = [];

                    if (Gate::allows('show contact_us')) {
                        $actions[] = sprintf(
                            '<a href="%s" class="btn btn-sm btn-info"><i class="fa fa-eye ti-xs"></i></a>',
                            route('dashboard.admins.contacts-show', ['id' => $id])
                        );
                    }

                    if (Gate::allows('edit contact_us') && $contact->status != '2') {
                        $actions[] = sprintf(
                            '<a href="%s" class="btn btn-sm btn-primary"><i class="fa fa-check ti-xs"></i></a>',
                            route('dashboard.admins.contacts-done', ['id' => $id])
                        );
                    }

                    if (Gate::allows('delete contact_us')) {
                        $actions[] = sprintf(
                            '<button type="button" class="btn btn-sm btn-danger delete-contact" data-id="%s">
                                <i class="fa fa-trash ti-xs"></i>
                            </button>',
                            $id
                        );
                    }

                    return implode(' ', $actions);
                })
                ->filterColumn('name', function($query, $keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('name', function ($query, $order) {
                    $query->orderBy('name', $order);
                })
                ->filterColumn('email', function ($query, $keyword) {
                    $query->where('email', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('email', function ($query, $order) {
                    $query->orderBy('email', $order);
                })
                ->filterColumn('phone', function($query, $keyword) {
                    $query->where('phone', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('phone', function ($query, $order) {
                    $query->orderBy('phone', $order);
                })
                ->filterColumn('subject', function ($query, $keyword) {
                    $query->where('subject', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('subject', function ($query, $order) {
                    $query->orderBy('subject', $order);
                })
                ->filterColumn('details', function($query, $keyword) {
                    $query->where('details', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('details', function ($query, $order) {
                    $query->orderBy('details', $order);
                })
                ->filterColumn('status', function ($query, $keyword) {
                    $query->where('status', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('status', function ($query, $order) {
                    $query->orderBy('status', $order);
                })
                ->filterColumn('created_at', function ($query, $keyword) {
                    $query->where('created_at', 'like', '%' . $keyword . '%');
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('themes.default.back.admins.pages.contacts.contacts-list');
    }

    public function show(Request $request)
    {
        if (!Gate::allows('show contact_us')) {
            return view('themes/default/back.permission-denied');
        }

        $contact = Contact::findOrFail(decrypt($request->id));

        if ($contact->status == '0') {
            $contact->status = '1';
            $contact->save();
        }

        return view('themes/default/back.admins.pages.contacts.contacts-show', ['contact' => $contact]);
    }

    public function done(Request $request)
    {
        if (!Gate::allows('edit contact_us')) {
            return view('themes/default/back.permission-denied');
        }

        $contact = Contact::findOrFail(decrypt($request->id));

        $contact->status = '2';
        $contact->save();

        return redirect()->back()->with('success', __('l.Message marked contacted'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete contact_us')) {
            return view('themes/default/back.permission-denied');
        }

        $contact = Contact::findOrFail(decrypt($request->id));

        $contact->delete();

        return redirect()->back()->with('success', __('l.Message deleted successfully'));
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete contact_us')) {
            return view('themes.default.back.permission-denied');
        }

        $ids = explode(',', $request->ids);
        $contacts = Contact::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', __('l.Data is successfully deleted'));
    }
}