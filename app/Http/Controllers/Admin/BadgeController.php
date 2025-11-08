<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BadgeRequest;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Badge/Index', [
            'badges' => Badge::query()->orderBy('order_level')->with('creator:id,name', 'editor:id,name')->paginate(10)
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
     * @param BadgeRequest $request
     * @param FileUploadAction $fileAction
     * @return RedirectResponse
     */
    public function store(BadgeRequest $request, FileUploadAction $fileAction): RedirectResponse
    {
        Badge::query()->create([
            ...$request->validated(),
            'logo' => $request->validated('logo') ? $fileAction->upload($request->validated('logo')) : null,
        ]);

        return to_route('badge.index');
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
     * @param BadgeRequest $request
     * @param string $id
     * @param FileUploadAction $fileAction
     * @return RedirectResponse
     */
    public function update(BadgeRequest $request, string $id, FileUploadAction $fileAction): RedirectResponse
    {
        $badge = Badge::query()->findOrFail($id);
        $badge->update([
            ...$request->validated(),
            'logo' => $request->validated('logo') ? $fileAction->upload($request->validated('logo'), $badge->logo) : null,
        ]);

        return to_route('badge.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @param FileUploadAction $fileAction
     * @return RedirectResponse
     */
    public function destroy(string $id, FileUploadAction $fileAction)
    {
        $badge = Badge::query()->findOrFail($id);
        if($badge->logo) {
            $fileAction->delete($badge->logo);
        }
        $badge->delete();

        return to_route('badge.index');
    }
}
