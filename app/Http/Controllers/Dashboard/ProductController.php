<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $user = auth()->user();
        
        $products = Product::where('tenant_id', $user->tenant_id)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $this->authorize('create', Product::class);

        return view('dashboard.products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:product,service',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être positif.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type doit être "produit" ou "service".',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['is_active'] = $request->boolean('is_active', true);
        
        $product = Product::create($validated);

        return redirect()
            ->route('client.products.show', $product)
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        
        return view('dashboard.products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|in:product,service',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être positif.',
            'type.required' => 'Le type est obligatoire.',
            'type.in' => 'Le type doit être "produit" ou "service".',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        
        $product->update($validated);

        return redirect()
            ->route('client.products.show', $product)
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        $product->delete();

        return redirect()
            ->route('client.products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}
