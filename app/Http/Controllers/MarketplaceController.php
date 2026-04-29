<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(): Response
    {
        $listings = MarketplaceListing::query()
            ->with('user:id,name,avg_name,first_name,last_name,show_full_name')
            ->active()
            ->latestFirst()
            ->get()
            ->map(fn (MarketplaceListing $listing) => [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'category' => $listing->category,
                'price' => $listing->price,
                'condition' => $listing->condition,
                'contact_name' => $listing->contact_name,
                'contact_phone' => $listing->contact_phone,
                'image_urls' => $listing->image_urls,
                'image_url' => $listing->image_url,
                'owner' => $this->resolvePublicUserName($listing->user),
                'is_owner' => $listing->user_id === Auth::id(),
                'created_at' => $listing->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Marktplaats/Index', [
            'listings' => $listings,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Marktplaats/Create');
    }

    public function show(MarketplaceListing $listing): Response
    {
        $listing->load('user:id,name,avg_name,first_name,last_name,show_full_name');

        if (! $listing->is_active && $listing->user_id !== Auth::id()) {
            abort(404);
        }

        return Inertia::render('Marktplaats/Show', [
            'listing' => [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'category' => $listing->category,
                'price' => $listing->price,
                'condition' => $listing->condition,
                'contact_name' => $listing->contact_name,
                'contact_phone' => $listing->contact_phone,
                'image_urls' => $listing->image_urls,
                'owner' => $this->resolvePublicUserName($listing->user),
                'is_owner' => $listing->user_id === Auth::id(),
                'created_at' => $listing->created_at?->toDateTimeString(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'category' => ['required', 'in:wapen,munitie,accessoire,overig'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'condition' => ['nullable', 'string', 'max:80'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'max:5120'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('images')) {
            $paths = collect($request->file('images'))
                ->map(fn ($file) => $file->store('marketplace', 'public'))
                ->all();

            $validated['images'] = $paths;
            $validated['image'] = $paths[0] ?? null;
        }

        MarketplaceListing::create([
            ...$validated,
            'user_id' => Auth::id(),
            'contact_name' => $validated['contact_name'] ?: $this->resolvePublicUserName(Auth::user()),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('marktplaats.index')
            ->with('message', 'Advertentie geplaatst op de marktplaats.');
    }

    public function edit(MarketplaceListing $listing): Response
    {
        $this->authorizeOwner($listing);

        return Inertia::render('Marktplaats/Edit', [
            'listing' => [
                'id' => $listing->id,
                'title' => $listing->title,
                'description' => $listing->description,
                'category' => $listing->category,
                'price' => $listing->price,
                'condition' => $listing->condition,
                'contact_name' => $listing->contact_name,
                'contact_phone' => $listing->contact_phone,
                'image_urls' => $listing->image_urls,
                'image_url' => $listing->image_url,
                'is_active' => $listing->is_active,
            ],
        ]);
    }

    public function update(Request $request, MarketplaceListing $listing)
    {
        $this->authorizeOwner($listing);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:4000'],
            'category' => ['required', 'in:wapen,munitie,accessoire,overig'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'condition' => ['nullable', 'string', 'max:80'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'max:5120'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('images')) {
            $paths = collect($request->file('images'))
                ->map(fn ($file) => $file->store('marketplace', 'public'))
                ->all();

            $validated['images'] = $paths;
            $validated['image'] = $paths[0] ?? null;
        } else {
            unset($validated['images']);
        }

        $listing->update($validated);

        return redirect()
            ->route('marktplaats.index')
            ->with('message', 'Advertentie bijgewerkt.');
    }

    public function destroy(MarketplaceListing $listing)
    {
        $this->authorizeOwner($listing);
        $listing->delete();

        return redirect()
            ->route('marktplaats.index')
            ->with('message', 'Advertentie verwijderd.');
    }

    private function authorizeOwner(MarketplaceListing $listing): void
    {
        abort_if($listing->user_id !== Auth::id(), 403, 'Je kunt alleen je eigen advertentie beheren.');
    }

    private function resolvePublicUserName(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        if ($user->show_full_name && $user->first_name && $user->last_name) {
            return "{$user->first_name} {$user->last_name}";
        }

        return $user->avg_name ?: $user->name;
    }
}
