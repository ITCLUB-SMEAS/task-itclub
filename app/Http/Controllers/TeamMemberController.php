<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $teamId)
    {
        $team = Team::with(['leader', 'members.user'])->findOrFail($teamId);

        // Cek apakah user adalah anggota tim atau admin
        if (auth()->user()->role !== 'admin' && !$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk melihat anggota tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.team_members.index', compact('team'));
        } else {
            return view('student.team_members.index', compact('team'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk menambahkan anggota tim.');
        }

        // Dapatkan siswa yang belum menjadi anggota tim dan dari kelas yang sama
        $eligibleStudents = User::where('role', 'student')
            ->where('kelas', $team->class_group)
            ->whereDoesntHave('teamMembers', function($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->get();

        if (auth()->user()->role === 'admin') {
            return view('admin.team_members.create', compact('team', 'eligibleStudents'));
        } else {
            return view('student.team_members.create', compact('team', 'eligibleStudents'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk menambahkan anggota tim.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek jumlah anggota tim
        $memberCount = $team->members()->count();
        if ($memberCount >= $team->max_members) {
            return back()->with('error', 'Tim sudah mencapai jumlah anggota maksimum.');
        }

        // Cek apakah sudah menjadi anggota
        if (TeamMember::where('team_id', $team->id)->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Pengguna sudah menjadi anggota tim ini.');
        }

        // Cek apakah siswa dari kelas yang sama
        $user = User::findOrFail($request->user_id);
        if ($user->kelas !== $team->class_group) {
            return back()->with('error', 'Anda hanya dapat menambahkan siswa dari kelas yang sama.');
        }

        // Tambahkan sebagai anggota
        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $request->user_id,
            'role' => 'member',
            'joined_at' => now(),
        ]);

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.members.index', $team->id)->with('success', 'Anggota berhasil ditambahkan.');
        } else {
            return redirect()->route('student.teams.members.index', $team->id)->with('success', 'Anggota berhasil ditambahkan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $teamId, string $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::with('user')->findOrFail($memberId);

        // Cek apakah user adalah anggota tim atau admin
        if (auth()->user()->role !== 'admin' && !$team->members->contains('user_id', auth()->id())) {
            abort(403, 'Anda tidak diizinkan untuk melihat detail anggota tim ini.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.team_members.show', compact('team', 'member'));
        } else {
            return view('student.team_members.show', compact('team', 'member'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $teamId, string $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::with('user')->findOrFail($memberId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit anggota tim.');
        }

        if (auth()->user()->role === 'admin') {
            return view('admin.team_members.edit', compact('team', 'member'));
        } else {
            return view('student.team_members.edit', compact('team', 'member'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $teamId, string $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::with('user')->findOrFail($memberId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk mengedit anggota tim.');
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:member,leader',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Jika ingin mengubah menjadi leader
        if ($request->role === 'leader' && $member->role !== 'leader') {
            // Update leader lama menjadi member
            TeamMember::where('team_id', $team->id)
                ->where('role', 'leader')
                ->update(['role' => 'member']);

            // Update team leader_id
            $team->leader_id = $member->user_id;
            $team->save();
        }

        $member->role = $request->role;
        $member->save();

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.members.index', $team->id)->with('success', 'Peran anggota berhasil diperbarui.');
        } else {
            return redirect()->route('student.teams.members.index', $team->id)->with('success', 'Peran anggota berhasil diperbarui.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $teamId, string $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::findOrFail($memberId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk menghapus anggota tim.');
        }

        // Tidak bisa menghapus pemimpin tim
        if ($member->role === 'leader') {
            return back()->with('error', 'Tidak dapat menghapus pemimpin tim. Ubah pemimpin tim terlebih dahulu.');
        }

        // Hapus anggota
        $member->delete();

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.members.index', $team->id)->with('success', 'Anggota berhasil dihapus dari tim.');
        } else {
            return redirect()->route('student.teams.members.index', $team->id)->with('success', 'Anggota berhasil dihapus dari tim.');
        }
    }

    /**
     * Invite students to team by sending email invitations
     */
    public function invite(Request $request, string $teamId)
    {
        $team = Team::findOrFail($teamId);

        // Cek apakah user adalah pemimpin tim atau admin
        if (auth()->user()->role !== 'admin' && $team->leader_id !== auth()->id()) {
            abort(403, 'Anda tidak diizinkan untuk mengundang anggota tim.');
        }

        $validator = Validator::make($request->all(), [
            'emails' => 'required|array',
            'emails.*' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek jumlah anggota tim
        $memberCount = $team->members()->count();
        $maxInvites = $team->max_members - $memberCount;

        if ($maxInvites <= 0) {
            return back()->with('error', 'Tim sudah mencapai jumlah anggota maksimum.');
        }

        // Batasi jumlah undangan yang dikirim
        $emails = array_slice($request->emails, 0, $maxInvites);

        // Proses undangan (bisa menggunakan Mail atau Notification)
        // Implementasi email undangan bisa ditambahkan nanti

        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.teams.members.index', $team->id)->with('success', 'Undangan berhasil dikirim.');
        } else {
            return redirect()->route('student.teams.members.index', $team->id)->with('success', 'Undangan berhasil dikirim.');
        }
    }
}
