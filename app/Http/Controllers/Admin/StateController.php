<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StateRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\State;
use Inertia\Response as InertiaResponse;
use App\Http\Resources\Admin\StateResource;
use App\Http\Resources\Admin\CountryResource;
use App\Models\Country;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @var AnonymousResourceCollection<StateResource>
     * @return InertiaResponse
     */
    public function index() : InertiaResponse
    {
        $state = State::query()
        ->with(['country:id,name','creator:id,name','editor:id,name'])
        ->search(['name','code','country.name'], request()->search);
        return inertia('Shipping/State/Index', [
            'states' => StateResource::collection($state),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return inertia('Shipping/State/Create', [
            'countries' => CountryResource::collection(Country::query()->select(['id','name'])->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param StateRequest $request
     * @return RedirectResponse
     */
    public function store(StateRequest $request) : RedirectResponse
    {
        State::create($request->validated());

        return to_route('state.index');
    }

    public function bulkCreate()
    {
        return inertia('Shipping/State/BulkCreate', [
             'countries' => CountryResource::collection(Country::query()->select('id','name')->get()),
        ]);
    }
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'country_id' => 'required',
            'states' => 'required|array',
            'states.*.name' => 'required|string|max:255|unique:states,name',
            'states.*.code' => 'nullable|string|max:255',
            'states.*.shipping_charge' => 'nullable|numeric|min:0',
            'states.*.tax_percentage' => 'nullable|numeric|min:0',
            'states.*.vat_percentage' => 'nullable|numeric|min:0',
        ]);
        DB::beginTransaction();
        try {
            foreach ($request->states as $state) {
                State::create([
                    'country_id' => $request->country_id,
                    'name' => $state['name'],
                    'postal_code' => $cityData['postal_code'] ?? null,
                    'shipping_charge' => $state['shipping_charge'],
                    'tax_percentage' => $state['tax_percentage'],
                    'vat_percentage' => $state['vat_percentage'],
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }

        return to_route('state.index')->with('success', 'State created successfully!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * @var StateResource
     * @var AnonymousResourceCollection<CountryResource>
     * @return InertiaResponse
     */
    public function edit(string $id)
    {
        return inertia('Shipping/State/Edit', [
            'state' => State::with('country')->findOrFail($id),
            'countries' => CountryResource::collection(Country::query()->select(['id','name'])->get()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param StateRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(StateRequest $request, string $id) : RedirectResponse
    {
        State::findOrFail($id)->update($request->validated());

        return to_route('state.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        State::findOrFail($id)->delete();

        return to_route('state.index');
    }
}
