<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ConsumeUserJob;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $users = User::all();

            return [
                'status' => 200,
                'message' => 'berhasil mendapatkan user',
                'data' => $users
            ];
        }catch(\Exception $e){
            return [
                'status' => 400,
                'message' => 'Gagal mendapatkan data user',
                'data' => $e->getMessage()
            ];
        }
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
    public function store()
    {
      
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $user = User::find($id);

            return [
                'status' => 200,
                'message' => 'berhasil mendapatkan user',
                'data' => $user
            ];
        }catch(\Exception $e){
            return [
                'status' => 400,
                'message' => 'Gagal mendapatkan data user',
                'data' => $e->getMessage()
            ];
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
