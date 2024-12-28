<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\User;
use App\Notifications\BookingNotification;
use Carbon\Carbon;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Booking::with('user', 'driver.user', 'receipt');

            if (auth()->user()->role == 'passenger') {
                $data = $data->where('user_id', auth()->user()->id);
            } else {
                $data = $data->whereHas('driver', function ($query) {
                    $query->where('user_id', auth()->user()->id);
                });
            }

            if ($request->status == 'upcoming') {
                $data = $data->whereIn('status', ['upcoming', 'pending']);
            } else if ($request->status == 'ongoing') {
                $data = $data->whereIn('status', ['ongoing', 'paid']);
            } else {
                $data = $data->where('status', $request->status);
            }

            $data = $data->orderBy('created_at', 'desc')->get();

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
            $data = Booking::findOrFail($id);
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
            $request->merge([
                'user_id' => auth()->user()->id,
                'status' => 'pending',
                'booking_date' => Carbon::parse($request->input('booking_date'))->toDateString(),
                'booking_time' => Carbon::parse($request->input('booking_time'))->toTimeString(),
            ]);

            $data = Booking::create($request->all());

            $driver = Driver::findOrFail($request->driver_id);
            $user = User::findOrFail($driver->user_id);
            $user->notify(new BookingNotification([
                'title' => 'New Booking Request',
                'message' => 'You have a new booking request from ' . auth()->user()->name,
                'avatar' => auth()->user()->avatar_url,
                'url' => '/tabs/booking'
            ]));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Booking successfully created.',
                'data' => $data,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the resource.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = Booking::with('driver.user', 'user')->findOrFail($id);
            $data->update($request->all());

            $driver = User::findOrFail($data->driver->user_id);
            $passenger = User::findOrFail($data->user_id);

            if (auth()->user()->role == 'passenger') {
                $driver->notify(new BookingNotification([
                    'title' => 'Booking Status Updated',
                    'message' => 'Your booking status for trip to ' . $data->to . ' has been updated by ' . $passenger->name,
                    'avatar' => $passenger->avatar_url,
                    'url' => '/tabs/booking'
                ]));
            } else {
                $passenger->notify(new BookingNotification([
                    'title' => 'Booking Status Updated',
                    'message' => 'Your booking status for trip to ' . $data->to . ' has been updated by ' . $driver->name,
                    'avatar' => $driver->avatar_url,
                    'url' => '/tabs/booking'
                ]));
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Booking successfully updated.',
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
            Booking::findOrFail($id)->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Booking successfully deleted.',
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
