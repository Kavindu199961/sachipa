<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    public function index()
    {
        $search = request('search');
        $userId = Auth::id();

        $stocks = Stock::where('user_id', $userId)
            ->when($search, function ($query) use ($search) {
                $query->where('item_name', 'like', '%' . $search . '%')
                    ->orWhere('item_code', 'like', '%' . $search . '%') // Added item_code to search
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('vender', 'like', '%' . $search . '%');
            })
            ->orderBy('stock_date', 'desc')
            ->paginate(10);

        return view('user.stock.index', compact('stocks'));
    }

    public function create()
    {
        return view('user.stock.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_code' => 'nullable|string|max:255|unique:stock,item_code', // Added item_code validation
                'item_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'barcode' => 'nullable|string|max:255',
                'cost' => 'required|numeric|min:0',
                'whole_sale_price' => 'nullable|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
                'vender' => 'nullable|string|max:255',
                'stock_date' => 'required|date',
                'quantity' => 'required|integer|min:0',
            ]);

            $validated['user_id'] = Auth::id(); // Add authenticated user

            // If item_code is not provided, let the model generate it automatically
            if (empty($validated['item_code'])) {
                unset($validated['item_code']); // Let the model handle auto-generation
            }

            Stock::create($validated);

            return redirect()->route('user.stock.index')
                ->with('success', 'Stock item created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check the form fields.');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database error occurred. Please try again.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    public function show($id)
    {
        $stock = Stock::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('user.stock.show', compact('stock'));
    }

    public function edit($id)
    {
        $stock = Stock::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if (request()->ajax()) {
            return response()->json($stock);
        }

        return view('user.stock.edit', compact('stock'));
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        try {
            $validated = $request->validate([
                'item_code' => 'nullable|string|max:255|unique:stock,item_code,' . $id, // Added with ignore current record
                'item_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'barcode' => 'nullable|string|max:255',
                'cost' => 'required|numeric|min:0',
                'whole_sale_price' => 'nullable|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
                'vender' => 'nullable|string|max:255',
                'stock_date' => 'required|date',
                'quantity' => 'required|integer|min:0',
            ]);

            // If item_code is empty and the model has auto-generation, remove it to keep existing value
            if (empty($validated['item_code']) && !empty($stock->item_code)) {
                unset($validated['item_code']); // Keep existing item_code
            }

            $stock->update($validated);

            return redirect()->route('user.stock.index')
                ->with('success', 'Stock item updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the stock item: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $stock = Stock::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            $itemName = $stock->item_name;
            $stock->delete();

            return redirect()->route('user.stock.index')
                ->with('success', 'Stock item (' . $itemName . ') deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.stock.index')
                ->with('error', 'Failed to delete stock item: ' . $e->getMessage());
        }
    }

    public function vendorShow($vendor)
    {
        $items = Stock::where('vender', $vendor)
            ->where('user_id', Auth::id())
            ->orderBy('item_name')
            ->get();

        if (request()->ajax()) {
            return response()->json($items);
        }

        return view('user.stock.vendor-show', compact('items', 'vendor'));
    }

    public function getStats()
    {
        try {
            $userId = Auth::id();

            $stats = [
                'total_items' => Stock::where('user_id', $userId)->count(),
                'total_quantity' => Stock::where('user_id', $userId)->sum('quantity'),
                'total_investment' => Stock::where('user_id', $userId)->sum(DB::raw('cost * quantity')),
                'total_wholesale_value' => Stock::where('user_id', $userId)->sum(DB::raw('whole_sale_price * quantity')),
                'total_retail_value' => Stock::where('user_id', $userId)->sum(DB::raw('retail_price * quantity')),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if item_code exists (for AJAX validation)
     */
    public function checkItemCode(Request $request)
    {
        $itemCode = $request->input('item_code');
        $id = $request->input('id'); // For update validation

        $query = Stock::where('user_id', Auth::id())
            ->where('item_code', $itemCode);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'available' => !$exists
        ]);
    }
}