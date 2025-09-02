<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // Admin melihat semua tim
            $teams = Team::with('leader', 'members.user')->latest()->paginate(10);
            return view('admin.teams.index', compact('teams'));
        } else {
            // Siswa melihat tim mereka
            $userTeams = $user->teams()->with('leader', 'members.user')->get();
            $availableTeams = Team::where('class_group', $user->kelas)
                ->where('is_active', true)
                ->whereDoesntHave('members', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->get();

            return view('student.teams.index', compact('userTeams', 'availableTeams'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $students = User::where('role', 'student')->get();
            return view('admin.teams.create', compact('students'));
        }

        return view('student.teams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'class_group' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_members' => 'required|integer|min:2|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $team = new Team();
        $team->name = $request->name;
        $team->description = $request->description;
        $team->class_group = $request->class_group;
        $team->max_members = $request->max_members;
        $team->leader_id = auth()->user()->role === 'admin' && $request->leader_id ? $request->leader_id : auth()->id();
        $team->team_code = strtoupper(Str::random(6));
        $team->is_active = true;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('team-logos', 'public');
            $team->logo = $logoPath;
        }

        $team->save();

        // Tambahkan pembuat tim sebagai anggota tim secara otomatis
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $team->leader_id,
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.index')->with('success', 'Tim berhasil dibuat.');
        } else {
            return redirect()->route('student.teams.index')->with('success', 'Tim berhasil dibuat.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = Team::with(['leader', 'members.user', 'assignments.task'])->findOrFail($id);

        // Cek apakah user adalah anggota tim atau admin
        if (auth()->user()->role !== 'admin' && !$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk melihat tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.teams.show', compact('team'));
        } else {
            return view('student.teams.show', compact('team'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $team = Team::findOrFail($id);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            $students = User::where('role', 'student')->get();
            return view('admin.teams.edit', compact('team', 'students'));
        } else {
            return view('student.teams.edit', compact('team'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = Team::findOrFail($id);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit tim ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'class_group' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_members' => 'required|integer|min:2|max:10',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $team->name = $request->name;
        $team->description = $request->description;
        $team->class_group = $request->class_group;
        $team->max_members = $request->max_members;

        // Hanya admin yang dapat mengubah pemimpin tim
        if (auth()->user()->role === 'admin' && $request->has('leader_id')) {
            $team->leader_id = $request->leader_id;

            // Update role anggota lama dan baru
            TeamMember::where('team_id', $team->id)
                ->where('role', 'leader')
                ->update(['role' => 'member']);

            $memberExists = TeamMember::where('team_id', $team->id)
                ->where('user_id', $request->leader_id)
                ->exists();

            if ($memberExists) {
                TeamMember::where('team_id', $team->id)
                    ->where('user_id', $request->leader_id)
                    ->update(['role' => 'leader']);
            } else {
                TeamMember::create([
                    'team_id' => $team->id,
                    'user_id' => $request->leader_id,
                    'role' => 'leader',
                    'joined_at' => now(),
                ]);
            }
        }

        if (isset($request->is_active)) {
            $team->is_active = $request->is_active;
        }

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($team->logo && Storage::disk('public')->exists($team->logo)) {
                Storage::disk('public')->delete($team->logo);
            }

            $logoPath = $request->file('logo')->store('team-logos', 'public');
            $team->logo = $logoPath;
        }

        $team->save();

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.show', $team->id)->with('success', 'Tim berhasil diperbarui.');
        } else {
            return redirect()->route('student.teams.show', $team->id)->with('success', 'Tim berhasil diperbarui.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::findOrFail($id);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk menghapus tim ini.');
        }

        // Hapus logo tim jika ada
        if ($team->logo && Storage::disk('public')->exists($team->logo)) {
            Storage::disk('public')->delete($team->logo);
        }

        // TeamMember dan TeamAssignment akan otomatis dihapus melalui cascading delete di migrasi
        $team->delete();

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.index')->with('success', 'Tim berhasil dihapus.');
        } else {
            return redirect()->route('student.teams.index')->with('success', 'Tim berhasil dihapus.');
        }
    }

    /**
     * Join a team using a team code
     */
    public function joinByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $team = Team::where('team_code', strtoupper($request->team_code))->first();

        if (!$team) {
            return back()->with('error', 'Kode tim tidak valid.');
        }

        if (!$team->is_active) {
            return back()->with('error', 'Tim ini sudah tidak aktif.');
        }

        if ($team->class_group !== auth()->user()->kelas) {
            return back()->with('error', 'Anda tidak dapat bergabung dengan tim dari kelas lain.');
        }

        $memberCount = $team->members()->count();
        if ($memberCount >= $team->max_members) {
            return back()->with('error', 'Tim sudah mencapai jumlah anggota maksimum.');
        }

        // Cek apakah sudah menjadi anggota
        if (TeamMember::where('team_id', $team->id)->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'Anda sudah menjadi anggota tim ini.');
        }

        // Tambahkan sebagai anggota
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => auth()->id(),
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('student.teams.show', $team->id)->with('success', 'Berhasil bergabung dengan tim.');
    }

    /**
     * Generate a new team code
     */
    public function regenerateCode(string $id)
    {
        $team = Team::findOrFail($id);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk melakukan tindakan ini.');
        }

        $team->team_code = strtoupper(Str::random(6));
        $team->save();

        return back()->with('success', 'Kode tim berhasil diperbaharui.');
    }

    /**
     * Toggle team active status
     */
    public function toggleStatus(string $id)
    {
        $team = Team::findOrFail($id);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk melakukan tindakan ini.');
        }

        $team->is_active = !$team->is_active;
        $team->save();

        $status = $team->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Tim berhasil $status.");
    }

    /**
     * Remove a member from team
     */
    public function removeMember(string $teamId, string $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::findOrFail($memberId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk melakukan tindakan ini.');
        }

        // Tidak bisa menghapus pemimpin tim
        if ($member->role === 'leader') {
            return back()->with('error', 'Tidak dapat menghapus pemimpin tim. Ubah pemimpin tim terlebih dahulu.');
        }

        // Hapus anggota
        $member->delete();

        return back()->with('success', 'Anggota berhasil dihapus dari tim.');
    }
}
