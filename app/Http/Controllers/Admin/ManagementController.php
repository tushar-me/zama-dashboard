<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManagementRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;

class ManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Management/Index', [
            'admins' => Admin::query()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Inertia\Response
     */
    public function create(): Response
    {
        return inertia('Management/Create');
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\Admin\ManagementRequest $request
     * @param \App\Actions\FileUploadAction $fileAction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ManagementRequest $request, FileUploadAction $fileAction)
    {
        Admin::create([
            ...$request->validated(),
            'password' => Hash::make($request->validated('password')),
            'image' => $request->validated('photo') ? $fileAction->upload($request->validated('photo')) : null,
        ]);

        return to_route('management.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return inertia('Management/Show', [
            'admin' => Admin::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return inertia('Management/Edit', [
            'admin' => Admin::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ManagementRequest $request, string $id, FileUploadAction $fileAction)
    {
        $admin = Admin::findOrFail($id);
        $admin->update([
            ...$request->validated(),
            'password' => $request->validated('password') ? Hash::make($request->validated('password')) : $admin->password,
            'photo' => $request->validated('photo') ? $fileAction->upload($request->validated('photo'), $admin->photo) : $admin->photo,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $admin = Admin::findOrFail($id);
        if($admin->photo){
            Storage::disk(env('FILESYSTEM_DISK'))->delete($admin->photo);
        }
        $admin->delete();

        return to_route('management.index');
    }
}
