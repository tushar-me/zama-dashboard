<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdditionalShippingChargeRequest;
use App\Models\AdditionalShippingCharge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdiitionalShippingChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        return inertia('Shipping/Additional/Index', [
            'additionalShippingCharges' => AdditionalShippingCharge::query()
                ->with('creator:id,name', 'editor:id,name')
                ->search(['name', 'creator.name', 'editor.name'], $request->search)
                ->paginate($request->per_page ?? 10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not needed as creation will be handled via dialog in index
    }

    /**
     * Store a newly created resource in storage.
     * @param AdditionalShippingChargeRequest $request
     * @return RedirectResponse
     */
    public function store(AdditionalShippingChargeRequest $request): RedirectResponse
    {
        AdditionalShippingCharge::query()->create($request->validated());

        return to_route('additional-shipping-charge.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not needed as editing will be handled via dialog in index
    }

    /**
     * Update the specified resource in storage.
     * @param AdditionalShippingChargeRequest $request
     * @param AdditionalShippingCharge $additionalShippingCharge
     * @return RedirectResponse
     */
    public function update(AdditionalShippingChargeRequest $request, AdditionalShippingCharge $additionalShippingCharge): RedirectResponse
    {
        $additionalShippingCharge->update($request->validated());

        return to_route('additional-shipping-charge.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param AdditionalShippingCharge $additionalShippingCharge
     * @return RedirectResponse
     */
    public function destroy(AdditionalShippingCharge $additionalShippingCharge): RedirectResponse
    {
        $additionalShippingCharge->delete();

        return to_route('additional-shipping-charge.index');
    }
}
