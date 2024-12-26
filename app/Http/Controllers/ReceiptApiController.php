<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;

class ReceiptApiController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $path = $request->file('receipt')->store('receipts', 'public');
            $booking = Booking::findOrFail($request->bookingId);
            $booking->update([
                'status' => 'paid',
            ]);
            $data = Receipt::create([
                'booking_id' => $booking->id,
                'receipt' => $path,
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Receipt successfully created.',
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
}
