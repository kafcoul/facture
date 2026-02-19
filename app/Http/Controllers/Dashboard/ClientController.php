<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Domain\Client\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            })
            ->withCount('invoices')
            ->withSum('invoices', 'total')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        $this->authorize('create', Client::class);

        return view('dashboard.clients.create');
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->tenant_id;
        
        $client = Client::create($validated);

        return redirect()
            ->route('client.clients.show', $client)
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        $this->authorize('view', $client);
        
        $client->loadCount('invoices');
        $client->loadSum('invoices', 'total');
        
        $recentInvoices = $client->invoices()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.clients.show', compact('client', 'recentInvoices'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        
        return view('dashboard.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, Client $client)
    {
        $this->authorize('update', $client);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
        ]);

        $client->update($validated);

        return redirect()
            ->route('client.clients.show', $client)
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        
        // Check if client has invoices
        if ($client->invoices()->count() > 0) {
            return redirect()
                ->route('client.clients.index')
                ->with('error', 'Impossible de supprimer ce client car il a des factures associées.');
        }
        
        $client->delete();

        return redirect()
            ->route('client.clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
