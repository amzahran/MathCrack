<?php

namespace App\Http\Controllers\Web\Back\Admins\Pages\Teams;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TeamsController extends Controller
{
    public function index(Request $request)
    {
        if (!Gate::allows('show team_members')) {
            return view('themes/default/back.permission-denied');
        }

        $teams = Team::orderByDesc('id')->get();

        return view('themes/default/back.admins.pages.teams.teams-list', ['teams' => $teams]);
    }

    public function store(Request $request)
    {
        if (!Gate::allows('add team_members')) {
            return view('themes/default/back.permission-denied');
        }

        $image = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image = upload_to_public($image, 'images/team');
        }

        $team = new Team();
        $team->name = $request->name;
        $team->job = $request->job;
        $team->image = $image;
        $team->facebook = $request->facebook;
        $team->twitter = $request->twitter;
        $team->instagram = $request->instagram;
        $team->linkedin = $request->linkedin;
        $team->save();

        return redirect()->back()->with('success', __('l.Member created successfully'));
    }

    public function edit(Request $request)
    {
        if (!Gate::allows('edit team_members')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $team = Team::find($id);
        if (!$team) {
            return redirect()->back()->with('error', __('l.Member dose not exist '));
        }

        return view('themes/default/back.admins.pages.teams.teams-edit', ['team' => $team]);
    }

    public function update(Request $request)
    {
        if (!Gate::allows('edit team_members')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $team = Team::find($id);
        if (!$team) {
            return redirect()->back()->with('error', __('l.Member does not exist.'));
        }

        $image = $team->image;
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا تم تحميل صورة جديدة
            if (!empty($image) && file_exists(public_path($image))) {
                unlink(public_path($image));
            }

            $imageFile = $request->file('image');
            $image = upload_to_public($imageFile, 'images/team');
        }

        $team->name = $request->name;
        $team->job = $request->job;
        $team->image = $image;
        $team->facebook = $request->facebook;
        $team->twitter = $request->twitter;
        $team->instagram = $request->instagram;
        $team->linkedin = $request->linkedin;
        $team->save();

        return redirect()->back()->with('success', __('l.Member updated successfully'));
    }

    public function delete(Request $request)
    {
        if (!Gate::allows('delete team_members')) {
            return view('themes/default/back.permission-denied');
        }

        $encryptedId = $request->id;
        $id = decrypt($encryptedId);

        $team = Team::find($id);

        if (!$team) {
            return redirect()->back()->with('error', __('l.Member does not exist'));
        }

        if ($team->image != '') {
            $filePath = public_path($team->image);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $team->delete();

        return redirect()->back()->with('success', __('l.Member deleted successfully'));
    }
}