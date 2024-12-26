<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\Booking;

class ReceiptController extends Controller
{
    public function index()
    {
        $datas = Receipt::paginate(10);
        return view('receipt.manage', compact('datas'));
    }

    public function edit($id)
    {
        $data = Receipt::findOrFail($id);
        $bookings = Booking::all();

        return view('receipt.edit', compact('data', 'bookings'));
    }

    public function destroy($id)
    {
        Receipt::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function create()
    {
        $bookings = Booking::all();

        return view('receipt.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        Receipt::create($request->all());
        return redirect()->route('receipt.index')
            ->with('success', 'Receipt Successfully Added');
    }

    public function update(Request $request, $id)
    {
        Receipt::findOrFail($id)->update($request->all());
        return redirect()->route('receipt.index')
            ->with('success', 'Receipt Successfully Updated');
    }

    public function show($id)
    {
        $data = Receipt::findOrFail($id);
        return view('receipt.show', compact('data'));
    }
}
