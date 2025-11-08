<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Http\Resources\Admin\CountryResource;
use Inertia\Response as InertiaResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Actions\FileUploadAction;
use App\Http\Requests\Admin\CountryRequest;
use Illuminate\Http\RedirectResponse;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @var AnonymousResourceCollection<CountryResource>
     * @return InertiaResponse
     */
    public function index() : InertiaResponse
    {
        return inertia('Shipping/Country/Index', [
            'countries' => CountryResource::collection(Country::query()
                            ->with(['creator:id,name', 'editor:id,name'])
                            ->search(['code', 'name','phone_code'], request()->search)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Shipping/Country/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request, FileUploadAction $fileAction)
    {
        Country::create([
            ...$request->validated(),
            'flag' => $request->file('flag') ? $fileAction->upload($request->file('flag'), false, null,  200, 100) : null,
        ]);

        return to_route('country.index');
    }

    public function bulkStore(Request $request, FileUploadAction $fileAction)
    {
        $request->validate([
            'countries' => 'required|array',
            'countries.*.name' => 'required|string|max:255|unique:countries,name',
            'countries.*.code' => 'required|string|max:255',
            'countries.*.iso2' => 'required|string|max:255',
            'countries.*.iso3' => 'required|string|max:255',
            'countries.*.flag' => 'nullable|string|starts_with:data:image/,data:image/jpeg,data:image/png,data:image/webp',
            'countries.*.status' => 'required|boolean',
            'countries.*.currency' => 'required|string|max:255',
            'countries.*.currency_symbol' => 'required|string|max:255',
            'countries.*.tax_percentage' => 'required|numeric|min:0|max:100',
            'countries.*.shipping_charge' => 'required|numeric|min:0',
            'countries.*.vat_percentage' => 'required|numeric|min:0|max:100',
            'countries.*.order_level' => 'required|integer|min:0',
        ]);

        foreach ($request->validated('countries') as $country) {
            Country::create([
                ...$country,
                'flag' => $country['flag'] ? $fileAction->upload($country['flag']) : $country->flag,
            ]);
        }

        return to_route('country.index');
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
        return inertia('Shipping/Country/Edit',[
            'country' => Country::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param CountryRequest $request
     * @param string $id
     * @return RedirectResponse
     */
    public function update(CountryRequest $request, string $id, FileUploadAction $fileAction) : RedirectResponse
    {
        $country = Country::findOrFail($id);
        $country->update([
            ...$request->validated(),
            'flag' => $request->hasFile('flag') ? $fileAction->upload($request->file('flag')) : $country->getRawOriginal('flag'),
        ]);

        return to_route('country.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, FileUploadAction $fileAction)
    {
        $country = Country::findOrFail($id);
        $country->flag ? $fileAction->delete($country->flag) : '';
        $country->delete();
        return to_route('country.index');
    }
}
