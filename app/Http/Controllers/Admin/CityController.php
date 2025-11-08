<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Admin\CityResource;
use App\Http\Resources\Admin\StateResource;
use App\Http\Resources\Admin\CountryResource;
use App\Http\Requests\Admin\CityRequest;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     * @var AnonymousResourceCollection<CityResource>
     * @return InertiaResponse
     */
    public function index() : InertiaResponse
    {
        $cities = City::query()
            ->with(['state:id,name', 'country:id,name','creator:id,name','editor:id,name'])
            ->search(['name', 'code', 'country.name','state.name'], request()->search);

        return inertia('Shipping/City/Index', [
            'cities' => CityResource::collection($cities)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Shipping/City/Create', [
            'countries' => CountryResource::collection(Country::query()->select('id','name')->with('states:id,country_id,name')->get()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CityRequest $request)
    {
        City::create($request->validated());

        return to_route('city.index');
    }
    public function bulkCreate()
    {
        return inertia('Shipping/City/BulkCreate', [
             'countries' => CountryResource::collection(Country::query()->select('id','name')->with('states:id,country_id,name')->get()),
        ]);
    }
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'country_id' => 'required',
            'state_id' => 'required',
            'cities' => 'required|array',
            'cities.*.name' => 'required|string|max:255',
            'cities.*.code' => 'nullable|string|max:255',
            'cities.*.postal_code' => 'nullable|string|max:255',
            'cities.*.timezone' => 'nullable|string|max:255',
            'cities.*.area_code' => 'nullable|string|max:255',
            'cities.*.shipping_charge' => 'nullable|numeric|min:0',
            'cities.*.tax_percentage' => 'nullable|numeric|min:0',
            'cities.*.vat_percentage' => 'nullable|numeric|min:0',
            'cities.*.latitude' => 'nullable|numeric|min:-90|max:90',
            'cities.*.longitude' => 'nullable|numeric|min:-180|max:180',
        ]);
        DB::beginTransaction();
        try {
            foreach ($request->cities as $cityData) {
                City::create([
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'name' => $cityData['name'],
                    'postal_code' => $cityData['postal_code'] ?? null,
                    'timezone' => $cityData['timezone'] ?? null,
                    'area_code' => $cityData['area_code'] ?? null,
                    'shipping_charge' => $cityData['shipping_charge'],
                    'tax_percentage' => $cityData['tax_percentage'],
                    'vat_percentage' => $cityData['vat_percentage'],
                    'latitude' => $cityData['latitude'] ?? null,
                    'longitude' => $cityData['longitude'] ?? null,
                    'is_default' => $cityData['is_default'] ?? false,
                    'order_level' => $cityData['order_level'] ?? 0,
                    'status' => $cityData['status'] ?? true,
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }

        return to_route('city.index')->with('success', 'Cities created successfully!');
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
     */
    public function edit(string $id)
    {
        return inertia('Shipping/City/Edit', [
            'city' => City::query()->with('country:id,name','country.states:id,name,country_id')->findOrFail($id),
            'countries' => Country::query()->select(['id','name'])->with('states:id,name,country_id')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CityRequest $request, string $id) : RedirectResponse
    {
        City::findOrFail($id)->update($request->validated());

        return to_route('city.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        City::findOrFail($id)->delete();

        return to_route('city.index');
    }
}
