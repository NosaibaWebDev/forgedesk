<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role', 'client')
            ->managedByAdmin(auth()->id())
            ->withCount('projects')
            ->latest()
            ->paginate(15);

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[A-Za-z])(?=.*\d).+$/'],
        ], [
            'password.regex' => 'הסיסמה חייבת לכלול לפחות אות אחת ומספר אחד.',
        ]);

        $validated['role'] = 'client';
        $validated['admin_id'] = auth()->id();
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $client = User::create($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'הלקוח נוצר בהצלחה.');
    }

    public function show(User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);

        $client->load(['projects' => function ($q) {
            $q->latest();
        }]);

        return view('admin.clients.show', compact('client'));
    }

    public function edit(User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)->with('success', 'פרטי הלקוח עודכנו.');
    }

    public function destroy(User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);

        if ($client->projects()->count() > 0) {
            return back()->with('error', 'לא ניתן למחוק לקוח עם פרויקטים קיימים.');
        }

        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', 'הלקוח נמחק.');
    }
}
