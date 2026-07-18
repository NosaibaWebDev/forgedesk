<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\User;
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

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        $client = new User();
        $client->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'address' => $validated['address'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'admin_id' => auth()->id(),
            'is_active' => true,
        ]);
        $client->save();

        return redirect()->route('admin.clients.show', $client)->with('success', __('client_created'));
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

    public function update(UpdateClientRequest $request, User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);

        $validated = $request->validated();
        $isActive = $validated['is_active'] ?? $client->is_active;
        unset($validated['is_active']);

        $client->update($validated);
        $client->forceFill(['is_active' => $isActive])->save();

        return redirect()->route('admin.clients.show', $client)->with('success', __('client_updated'));
    }

    public function destroy(User $client)
    {
        abort_unless($client->isClient() && $client->admin_id === auth()->id(), 404);

        if ($client->projects()->count() > 0) {
            return back()->with('error', __('client_has_projects'));
        }

        $client->delete();
        return redirect()->route('admin.clients.index')->with('success', __('client_deleted'));
    }
}
