<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DriverApiController extends Controller
{
    public function index()
    {
        try {
            $data = Driver::with('user')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = Driver::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = Driver::updateOrCreate(
                ['user_id' => auth()->user()->id],
                $request->all()
            );
            $user = User::with('driver')->findOrFail(auth()->user()->id);
            DB::commit();
            $message = $data->wasRecentlyCreated
                ? 'Driver successfully created.'
                : 'Driver successfully updated.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $user,
            ], Response::HTTP_OK); // Use HTTP_OK (200) for both create and update
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the resource.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Driver::findOrFail($id);
            $data->update($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Driver successfully updated.',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the resource.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Driver::findOrFail($id)->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Driver successfully deleted.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the resource.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
