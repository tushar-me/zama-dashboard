<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentMethodRequest;
use App\Http\Resources\Admin\PaymentMethodResource;
use App\Models\PaymentMethod;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $methods = PaymentMethod::query()
        ->orderBy('order_level')
        ->search(['name'], request()->search);

        return inertia('Account/PaymentMethod', [
            'methods' => PaymentMethodResource::collection($methods),
            'search' => request()->search,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request, FileUploadAction $fileAction)
    {
        PaymentMethod::query()->create([
            ...$request->validated(),
            'logo' => $request->file('logo') ? $fileAction->upload($request->file('logo'), false, null, null,null) : null
        ]);

        return redirect()->back();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentMethodRequest $request, string $id, FileUploadAction $fileAction)
    {
        $method = PaymentMethod::query()->findOrFail($id);
        $method->update([
            ...$request->validated(),
            'logo' => $request->file('logo') ? $fileAction->upload($request->file('logo'), false, $method->logo, null, null) : $method->getRawOriginal('logo')
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $method = PaymentMethod::query()->findOrFail($id);
        $method->delete();

        return redirect()->back();
    }
}
