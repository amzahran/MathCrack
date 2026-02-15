<?php

namespace App\Http\Controllers\Web\Back\Admins\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Models\Country;
use App\Models\Level;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show students')) {
            return view('themes/default/back.permission-denied');
        }

        $settings = app('cached_data')['settings'];

        if (!isset($request->inactive) || $request->inactive == 0) {
            if ($request->ajax()) {
                $users = User::select([
                        'users.id',
                        'users.firstname',
                        'users.lastname',
                        'users.email',
                        'users.phone',
                        'users.image',
                        'users.level_id',
                        'users.created_at'
                    ])
                    ->with('level')
                    ->where('min_role', 0)
                    ->doesntHave('roles');

                if ($settings['emailVerified'] == 1) {
                    $users->whereNotNull('email_verified_at');
                }

                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('name', function($row) {
                        $firstChar = mb_substr($row->firstname, 0, 1, 'UTF-8');
                        $lastChar = mb_substr($row->lastname, 0, 1, 'UTF-8');
                        $initials = $firstChar . $lastChar;
                        $userImage = '';

                        if ($row->image && file_exists(public_path('images/usersProfile/' . $row->image))) {
                            $userImage = '<img src="' . asset('images/usersProfile/' . $row->image) . '"
                                             class="rounded-circle me-2"
                                             width="35" height="35"
                                             alt="' . htmlspecialchars($row->firstname) . '">';
                        } else {
                            $userImage = '<div class="rounded-circle me-2 d-inline-flex align-items-center justify-content-center"
                                             style="width: 35px; height: 35px; background-color: #e9ecef;">
                                             <span style="font-family: Arial, sans-serif;">' . htmlspecialchars($initials) . '</span>
                                         </div>';
                        }

                        return $userImage . '<span>' . htmlspecialchars($row->firstname . ' ' . $row->lastname) . '</span>';
                    })
                    ->filterColumn('name', function($query, $keyword) {
                        $query->where(function($q) use ($keyword) {
                            $q->where('users.firstname', 'like', "%{$keyword}%")
                              ->orWhere('users.lastname', 'like', "%{$keyword}%")
                              ->orWhereRaw("CONCAT(users.firstname, ' ', users.lastname) like ?", ["%{$keyword}%"]);
                        });
                    })
                    ->filterColumn('email', function($query, $keyword) {
                        $query->where('users.email', 'like', "%{$keyword}%");
                    })
                    ->filterColumn('phone', function($query, $keyword) {
                        $query->where('users.phone', 'like', "%{$keyword}%");
                    })
                    ->orderColumn('name', function ($query, $order) {
                        $query->orderByRaw("CONCAT(users.firstname, ' ', users.lastname) {$order}");
                    })
                    ->orderColumn('email', function ($query, $order) {
                        $query->orderBy('users.email', $order);
                    })
                    ->orderColumn('phone', function ($query, $order) {
                        $query->orderBy('users.phone', $order);
                    })
                    ->orderColumn('created_at', function ($query, $order) {
                        $query->orderBy('users.created_at', $order);
                    })
                    ->addColumn('action', function($row) {
                        $actionBtn = view('themes.default.back.admins.customers.action-buttons', ['row' => $row])->render();
                        return $actionBtn;
                    })
                    ->addColumn('level', function($row) {
                        return $row->level ? $row->level->name : __('l.No Level');
                    })
                    ->rawColumns(['action', 'name'])
                    ->make(true);
            }
            $inactiveUsers = 0;
        } else {
            if ($request->ajax()) {
                $users = User::onlyTrashed()
                    ->select([
                        'users.id',
                        'users.firstname',
                        'users.lastname',
                        'users.email',
                        'users.phone',
                        'users.deleted_at'
                    ])
                    ->where('min_role', 0)
                    ->doesntHave('roles');

                if ($settings['emailVerified'] == 1) {
                    $users->whereNotNull('email_verified_at');
                }

                return DataTables::eloquent($users)
                    ->addIndexColumn()
                    ->addColumn('name', function($row) {
                        return $row->firstname . ' ' . $row->lastname;
                    })
                    ->filterColumn('name', function($query, $keyword) {
                        $query->where(function($q) use ($keyword) {
                            $q->where('users.firstname', 'like', "%{$keyword}%")
                              ->orWhere('users.lastname', 'like', "%{$keyword}%");
                        });
                    })
                    ->addColumn('deleted_at', function($row) {
                        return \Carbon\Carbon::parse($row->deleted_at)->format('Y/m/d h:i A');
                    })
                    ->addColumn('action', function($row) {
                        $actionBtn = view('themes.default.back.admins.customers.inactive-action-buttons', ['row' => $row])->render();
                        return $actionBtn;
                    })
                    ->filterColumn('deleted_at', function($query, $keyword) {
                        $query->where('users.deleted_at', 'like', "%{$keyword}%");
                    })
                    ->orderColumn('deleted_at', function ($query, $order) {
                        $query->orderBy('users.deleted_at', $order);
                    })
                    ->rawColumns(['action'])
                    ->orderColumn('name', function ($query, $order) {
                        $query->orderBy('users.firstname', $order)
                              ->orderBy('users.lastname', $order);
                    })
                    ->make(true);
            }
            $inactiveUsers = 1;
        }

        return view('themes/default/back.admins.customers.customers-list', compact('inactiveUsers'));
    }

    public function show(Request $request)
    {
        if (!Gate::allows('show students')) {
            return view('themes/default/back.permission-denied');
        }

        $id=decrypt($request->id);

        $user=User::with('level')->findOrFail($id);

        return view('themes/default/back.admins.customers.customers-show', ['user' => $user]);
    }

    public function add(Request $request)
    {
        if (!Gate::allows('add students')) {
            return view('themes/default/back.permission-denied');
        }

        $countries = Country::all();
        $levels = Level::all();

        return view('themes/default/back.admins.customers.customers-add', ['countries' => $countries, 'levels' => $levels]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add students')) {
            return view('themes/default/back.permission-denied');
        }

        $request->validate([
            'firstname' => ['required', 'string', 'max:30'],
            'lastname' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:30',
            'zip_code' => 'nullable|string|max:25',
            'country' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:30',
            'level_id' => 'nullable|exists:levels,id',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $phone = str_replace(' ', '', $request['phone']);
        $phone = '+'.$request['phone_code'].$phone;

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $phone,
            'address' => $request->address,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'city' => $request->city,
            'country' => $request->country,
            'level_id' => $request->level_id,
            'password' => Hash::make($request->password),
        ]);

        $user->email_verified_at = now();
        $user->save();

        return redirect()->back()->with('success', __('l.Customer Account added successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $id=decrypt($request->id);

        $user=User::findOrFail($id);

        $countries = Country::all();
        $roles = Role::all();
        $levels = Level::all();

        return view('themes/default/back.admins.customers.customers-edit', ['user' => $user, 'countries' => $countries, 'roles' => $roles, 'levels' => $levels]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $input = $request->all();

        $id=decrypt($request->id);

        $rules = [
            'firstname' => 'required|string|max:30',
            'lastname' => 'required|string|max:60',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:30',
            'zip_code' => 'nullable|string|max:25',
            'country' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:30',
            'level_id' => 'nullable|exists:levels,id',
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $phone = str_replace(' ', '', $input['phone']);
        $phone = '+'.$input['phone_code'].$phone;

        $user = User::findOrFail($id);

        $user->firstname = $input['firstname'];
        $user->lastname = $input['lastname'];
        $user->email = $input['email'];
        $user->phone = $phone;
        $user->address = $input['address'];
        $user->state = $input['state'];
        $user->zip_code = $input['zip_code'];
        $user->country = $input['country'];
        $user->city = $input['city'];
        $user->level_id = $input['level_id'];
        $user->save();

        return redirect()->back()->with('success', __('l.Customer Profile updated successfully'));
    }

    public function updatepassword(Request $request)
    {
        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $validated = $request->validateWithBag('updatePassword', [
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $id=decrypt($request->id);

        $user = User::findOrFail($id);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', __('l.password updated successfully'));
    }

    public function inactive(Request $request)
    {
        if (!Gate::allows('delete students')) {
            return view('themes/default/back.permission-denied');
        }

        $id=decrypt($request->id);

        $user = User::findOrFail($id);

        if (basename($user->photo) != 'profile.png') {
            $path = public_path('images/users_profile/' . basename($user->photo));
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $user->delete();

        return redirect()->route('dashboard.admins.customers')->with('success', __('l.Customer Profile inactive successfully'));
    }

    public function active(Request $request)
    {

        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $userId = decrypt($request->query('id')); // استرجاع معرّف المستخدم

        $user = User::withTrashed()->findOrFail($userId); // البحث عن المستخدم (حتى المحذوفين)

        // استعادة المستخدم من الحذف
        $user->restore();

        return redirect()->back()->with('success', __('l.Customer has been restored and is now active'));
    }

    public function deleteinactive(Request $request)
    {

        if (!Gate::allows('delete students')) {
            return view('themes/default/back.permission-denied');
        }

        $userId = decrypt($request->query('id')); // استرجاع عرّف المستخدم

        $user = User::withTrashed()->findOrFail($userId);

        // حذف المستخد بشكل دائم
        $user->forceDelete();

        return redirect()->back()->with('success', __('l.Customer has been deleted permanently'));
    }

    public function deleteallinactive()
    {
        if (!Gate::allows('delete students')) {
            return view('themes/default/back.permission-denied');
        }

        User::onlyTrashed()->doesntHave('roles')->forceDelete();

        return redirect()->back()->with('success', __('l.All inactive customers have been deleted permanently'));
    }

    public function role(Request $request)
    {
        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $id=decrypt($request->id);

        $user = User::findOrFail($id);
        $user->syncRoles([]);
        $user->assignRole($request->role);

        return redirect()->back()->with('success', __('l.role assigned successfully'));
    }

    public function roledelete(Request $request)
    {
        if (!Gate::allows('edit students')) {
            return view('themes/default/back.permission-denied');
        }

        $id=decrypt($request->id);

        $user = User::findOrFail($id);
        $user->syncRoles([]);

        return redirect()->back()->with('success', __('l.roles deleted successfully'));
    }

    public function export(Request $request)
    {
        if (!Gate::allows('show students')) {
            return view('themes/default/back.permission-denied');
        }

        $query = User::query();

        if (!$request->inactive && $request->inactive != 1) {
            $query->whereNotNull('email_verified_at')
                  ->whereNull('deleted_at');
        } else {
            $query->onlyTrashed();
        }

        if ($request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('firstname', 'LIKE', "%{$searchValue}%")
                  ->orWhere('lastname', 'LIKE', "%{$searchValue}%")
                  ->orWhere('email', 'LIKE', "%{$searchValue}%")
                  ->orWhere('phone', 'LIKE', "%{$searchValue}%");
            });
        }

        if ($request->order) {
            $columnIndex = $request->order[0]['column'];
            if ($columnIndex > 0) {
                $columnName = $request->columns[$columnIndex]['name'];
                $columnDir = $request->order[0]['dir'];

                switch ($columnName) {
                    case 'name':
                        $query->orderByRaw("CONCAT(firstname, ' ', lastname) {$columnDir}");
                        break;
                    default:
                        if (in_array($columnName, ['id', 'email', 'phone', 'created_at'])) {
                            $query->orderBy($columnName, $columnDir);
                        }
                        break;
                }
            }
        }

        $users = $query->select([
            'users.id',
            'users.firstname',
            'users.lastname',
            'users.email',
            'users.phone',
            'users.level_id',
            'users.created_at'
        ])
        ->with('level')
        ->where('min_role', 0)
        ->doesntHave('roles')
        ->get();

        $counter = 1;
        $exportData = $users->map(function($user) use (&$counter) {
            return [
                '#' => $counter++,
                __('l.Customer Name') => $user->firstname . ' ' . $user->lastname,
                __('l.Email') => $user->email,
                __('l.Phone') => $user->phone,
                __('l.Level') => $user->level ? $user->level->name : __('l.No Level'),
                __('l.Created At') => $user->created_at->format('Y-m-d H:i:s')
            ];
        });

        switch($request->type) {
            case 'excel':
                return (new FastExcel($exportData))->download('customers.xlsx');
            case 'csv':
                return (new FastExcel($exportData))->download('customers.csv');
            case 'pdf':
                return response()->json(['data' => $exportData]);
            default:
                return response()->json(['data' => $exportData]);
        }
    }

    public function deleteSelected(Request $request)
    {
        if (!Gate::allows('delete students')) {
            return view('themes/default/back.permission-denied');
        }

        $ids = explode(',', $request->ids);

        if ($request->has('inactive') && $request->inactive == 1) {
            // حذف نهائي للمستخدمين المحذوفين مؤقتاً
            User::onlyTrashed()
                ->whereIn('id', $ids)
                ->doesntHave('roles')
                ->forceDelete();

            $message = __('l.Selected customers have been deleted permanently');
        } else {
            // حذف مؤقت للمستخدمين النشطين
            User::whereIn('id', $ids)
                ->doesntHave('roles')
                ->forceDelete();
                // ->delete();

            $message = __('l.Selected customers have been disabled');
        }

        return redirect()->back()->with('success', $message);
    }

    public function import(Request $request)
    {
        if (!Gate::allows('add students')) {
            return view('themes/default/back.permission-denied');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048'
            ]);

            try {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();

                if (in_array($extension, ['xlsx', 'xls'])) {
                    $spreadsheet = IOFactory::load($file->getPathname());
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();

                    // تخطي الصف الأول (العناوين)
                    array_shift($rows);

                    $imported = 0;
                    $errors = [];

                    foreach ($rows as $row) {
                        try {
                            if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                                throw new \Exception(__('l.Required fields are missing'));
                            }

                            // التحقق من فريدية البريد الإلكتروني
                            if (User::where('email', $row[2])->exists()) {
                                throw new \Exception(__('l.Email already exists') . ': ' . $row[2]);
                            }

                            $user = new User();
                            $user->firstname = $row[0] ?? '';
                            $user->lastname = $row[1] ?? '';
                            $user->email = $row[2] ?? '';
                            $user->phone = $row[3] ?? '';
                            $user->password = Hash::make($row[4] ?? '123456789');
                            $user->email_verified_at = now();

                            // الحقول الاختيارية
                            $user->address = $row[5] ?? null;
                            $user->state = $row[6] ?? null;
                            $user->zip_code = $row[7] ?? null;
                            $user->country = $row[8] ?? null;
                            $user->city = $row[9] ?? null;
                            $user->level_id = $row[10] ?? null;

                            $user->save();

                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = __('l.Error in line') . ' ' . ($imported + 2) . ': ' . $e->getMessage();
                        }
                    }
                } else {
                    $handle = fopen($file->getPathname(), "r");

                    // تخطي الصف الأول (العناوين)
                    $header = fgetcsv($handle);

                    $imported = 0;
                    $errors = [];

                    while (($data = fgetcsv($handle)) !== FALSE) {
                        try {
                            if (empty($data[0]) || empty($data[1]) || empty($data[2]) || empty($data[3])) {
                                throw new \Exception(__('l.Required fields are missing'));
                            }

                            // التحقق من فريدية البريد الإلكتروني
                            if (User::where('email', $data[2])->exists()) {
                                throw new \Exception(__('l.Email already exists') . ': ' . $data[2]);
                            }

                            $user = new User();
                            $user->firstname = $data[0] ?? '';
                            $user->lastname = $data[1] ?? '';
                            $user->email = $data[2] ?? '';
                            $user->phone = $data[3] ?? '';
                            $user->password = Hash::make($data[4] ?? '123456789');
                            $user->email_verified_at = now();

                            // الحقول الاختيارية
                            $user->address = $data[5] ?? null;
                            $user->state = $data[6] ?? null;
                            $user->zip_code = $data[7] ?? null;
                            $user->country = $data[8] ?? null;
                            $user->city = $data[9] ?? null;
                            $user->level_id = $data[10] ?? null;

                            $user->save();

                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = __('l.Error in line') . ' ' . ($imported + 2) . ': ' . $e->getMessage();
                        }
                    }

                    fclose($handle);
                }

                if (count($errors) > 0) {
                    return redirect()->back()->with('warning', __('l.Imported') . ' ' . $imported . ' ' . __('l.customers successfully') . '. ' . implode('<br>', $errors));
                }

                return redirect()->back()->with('success', __('l.Imported') . ' ' . $imported . ' ' . __('l.customers successfully'));

            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('l.An error occurred while importing the file:') . ' ' . $e->getMessage());
            }
        }

        return view('themes.default.back.admins.customers.customers-import');
    }
}