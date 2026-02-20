<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\TodayItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TodayItemController extends Controller
{
    /**
     * Display today's items page
     */
    public function index()
    {
        // Get today's saved items
        $todayItems = TodayItem::where('user_id', Auth::id())
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get available stock items for dropdown (only items with quantity > 0)
        $stocks = Stock::where('user_id', Auth::id())
            ->where('quantity', '>', 0)
            ->orderBy('item_name')
            ->get(['id', 'item_code', 'item_name', 'cost', 'quantity', 'barcode']);
        
        // Calculate total for today
        $totalToday = $todayItems->sum('total_cost');
        
        // Get session items from session (temporary items not saved yet)
        $sessionItems = session()->get('today_items_temp', []);
        
        return view('user.today_item.index', compact('stocks', 'todayItems', 'totalToday', 'sessionItems'));
    }

    /**
     * Get stock details by ID
     */
    public function getStockDetails($id)
    {
        try {
            $stock = Stock::where('user_id', Auth::id())
                ->where('id', $id)
                ->where('quantity', '>', 0)
                ->first(['id', 'item_code', 'item_name', 'cost', 'quantity', 'barcode']);
            
            if ($stock) {
                return response()->json([
                    'success' => true,
                    'stock' => $stock
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Stock not found or out of stock'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock by barcode
     */
    public function getStockByBarcode($barcode)
    {
        try {
            $stock = Stock::where('user_id', Auth::id())
                ->where('barcode', $barcode)
                ->where('quantity', '>', 0)
                ->first(['id', 'item_code', 'item_name', 'cost', 'quantity', 'barcode']);
            
            if ($stock) {
                return response()->json([
                    'success' => true,
                    'stock' => $stock
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Stock not found or out of stock'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stock by barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add item to temporary session
     */
    public function addToTemp(Request $request)
    {
        // Custom validation without using exists:stocks
        $validator = Validator::make($request->all(), [
            'stock_id' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get stock to verify quantity and existence
            $stock = Stock::where('user_id', Auth::id())
                ->where('id', $request->stock_id)
                ->first();

            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock item not found'
                ], 404);
            }

            if ($stock->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient quantity. Available: {$stock->quantity}"
                ], 422);
            }

            // Create temporary item
            $tempItem = [
                'id' => Str::random(10),
                'stock_id' => $stock->id,
                'item_code' => $stock->item_code,
                'item_name' => $stock->item_name,
                'cost' => (float)$stock->cost,
                'quantity' => (int)$request->quantity,
                'total_cost' => (float)($stock->cost * $request->quantity),
                'available_qty' => (int)$stock->quantity
            ];

            // Get existing temp items from session
            $tempItems = session()->get('today_items_temp', []);
            
            // Check if item already exists in temp
            $existingIndex = null;
            foreach ($tempItems as $index => $item) {
                if ($item['stock_id'] == $stock->id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                // Update existing item quantity
                $newQty = $tempItems[$existingIndex]['quantity'] + $request->quantity;
                if ($newQty > $stock->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Total quantity would exceed available stock. Available: {$stock->quantity}"
                    ], 422);
                }
                $tempItems[$existingIndex]['quantity'] = $newQty;
                $tempItems[$existingIndex]['total_cost'] = (float)($stock->cost * $newQty);
            } else {
                // Add new item
                $tempItems[] = $tempItem;
            }

            // Save to session
            session()->put('today_items_temp', $tempItems);

            // Calculate new total
            $total = collect($tempItems)->sum('total_cost');

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'items' => $tempItems,
                'total' => (float)$total,
                'count' => count($tempItems)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from temporary session
     */
    public function removeFromTemp($id)
    {
        try {
            $tempItems = session()->get('today_items_temp', []);
            
            $tempItems = array_filter($tempItems, function($item) use ($id) {
                return $item['id'] != $id;
            });

            // Reindex array
            $tempItems = array_values($tempItems);
            
            session()->put('today_items_temp', $tempItems);

            $total = collect($tempItems)->sum('total_cost');

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
                'items' => $tempItems,
                'total' => (float)$total,
                'count' => count($tempItems)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all temporary items
     */
    public function clearTemp()
    {
        try {
            session()->forget('today_items_temp');
            
            return response()->json([
                'success' => true,
                'message' => 'All temporary items cleared'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save all temporary items to database and reduce stock
     */
    public function saveAll(Request $request)
    {
        $tempItems = session()->get('today_items_temp', []);

        if (empty($tempItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No items to save'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            $sessionId = Str::uuid()->toString();
            $userId = Auth::id();
            $savedItems = [];
            
            foreach ($tempItems as $tempItem) {
                // Get stock with lock to prevent race conditions
                $stock = Stock::where('id', $tempItem['stock_id'])
                    ->where('user_id', $userId)
                    ->lockForUpdate()
                    ->first();
                
                if (!$stock) {
                    throw new \Exception('Stock item not found: ' . $tempItem['item_name']);
                }
                
                // Double-check quantity
                if ($stock->quantity < $tempItem['quantity']) {
                    throw new \Exception("Insufficient quantity for {$stock->item_name}. Available: {$stock->quantity}, Requested: {$tempItem['quantity']}");
                }
                
                // Calculate total cost
                $totalCost = $stock->cost * $tempItem['quantity'];
                
                // Create today item record
                $todayItem = TodayItem::create([
                    'item_code' => $stock->item_code,
                    'item_name' => $stock->item_name,
                    'description' => $stock->description,
                    'cost' => $stock->cost,
                    'quantity' => $tempItem['quantity'],
                    'total_cost' => $totalCost,
                    'stock_id' => $stock->id,
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'selection_date' => now()
                ]);
                
                // Reduce stock quantity
                $stock->quantity -= $tempItem['quantity'];
                $stock->save();
                
                $savedItems[] = $todayItem;
            }
            
            // Clear temporary session
            session()->forget('today_items_temp');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($savedItems) . ' items saved successfully and stock updated',
                'items' => $savedItems,
                'total' => (float)collect($savedItems)->sum('total_cost')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove saved item and restore stock
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $todayItem = TodayItem::where('user_id', Auth::id())
                ->where('id', $id)
                ->firstOrFail();
            
            // Restore stock quantity
            if ($todayItem->stock_id) {
                $stock = Stock::where('id', $todayItem->stock_id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                if ($stock) {
                    $stock->quantity += $todayItem->quantity;
                    $stock->save();
                }
            }
            
            $todayItem->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed and stock restored'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Clear all saved items for today
     */
    public function clearAllSaved()
    {
        DB::beginTransaction();
        
        try {
            $todayItems = TodayItem::where('user_id', Auth::id())
                ->whereDate('created_at', today())
                ->get();
            
            if ($todayItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No saved items to clear'
                ], 422);
            }
            
            foreach ($todayItems as $item) {
                if ($item->stock_id) {
                    $stock = Stock::where('id', $item->stock_id)
                        ->where('user_id', Auth::id())
                        ->first();
                    
                    if ($stock) {
                        $stock->quantity += $item->quantity;
                        $stock->save();
                    }
                }
                
                $item->delete();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'All items cleared and stock restored'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get temporary items
     */
    public function getTempItems()
    {
        try {
            $tempItems = session()->get('today_items_temp', []);
            $total = collect($tempItems)->sum('total_cost');
            
            return response()->json([
                'success' => true,
                'items' => $tempItems,
                'total' => (float)$total,
                'count' => count($tempItems)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching temporary items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update temporary item quantity
     */
    public function updateTempQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $tempItems = session()->get('today_items_temp', []);
            $updated = false;
            
            foreach ($tempItems as &$item) {
                if ($item['id'] == $id) {
                    // Check if new quantity is available
                    $stock = Stock::where('user_id', Auth::id())
                        ->where('id', $item['stock_id'])
                        ->first();
                    
                    if (!$stock) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Original stock item not found'
                        ], 404);
                    }
                    
                    if ($stock->quantity < $request->quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient quantity. Available: {$stock->quantity}"
                        ], 422);
                    }
                    
                    $item['quantity'] = $request->quantity;
                    $item['total_cost'] = (float)($item['cost'] * $request->quantity);
                    $updated = true;
                    break;
                }
            }
            
            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in temporary list'
                ], 404);
            }
            
            session()->put('today_items_temp', $tempItems);
            
            $total = collect($tempItems)->sum('total_cost');
            
            return response()->json([
                'success' => true,
                'message' => 'Quantity updated successfully',
                'items' => $tempItems,
                'total' => (float)$total,
                'count' => count($tempItems)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating quantity: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Search saved items by date
 */
public function searchByDate(Request $request)
{
    $request->validate([
        'date' => 'required|date'
    ]);

    try {
        $date = $request->date;
        
        $todayItems = TodayItem::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $total = $todayItems->sum('total_cost');
        
        // Get stocks for dropdown (still needed for adding new items)
        $stocks = Stock::where('user_id', Auth::id())
            ->where('quantity', '>', 0)
            ->orderBy('item_name')
            ->get(['id', 'item_code', 'item_name', 'cost', 'quantity', 'barcode']);
        
        // Get session items
        $sessionItems = session()->get('today_items_temp', []);
        
        // Calculate total for the searched date
        $totalToday = $total;
        
        // Pass a flag to indicate we're viewing a different date
        $viewingDate = $date;
        
        return view('user.today_item.index', compact(
            'stocks', 
            'todayItems', 
            'totalToday', 
            'sessionItems',
            'viewingDate'
        ));
        
    } catch (\Exception $e) {
        return redirect()->route('user.today_item.index')
            ->with('error', 'Error searching by date: ' . $e->getMessage());
    }
}

/**
 * Get saved items by date (AJAX for modal or inline update)
 */
public function getItemsByDate(Request $request)
{
    $request->validate([
        'date' => 'required|date'
    ]);

    try {
        $date = $request->date;
        
        $items = TodayItem::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'item_name' => $item->item_name,
                    'cost' => number_format($item->cost, 2),
                    'quantity' => $item->quantity,
                    'total_cost' => number_format($item->total_cost, 2),
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'can_delete' => false // Past dates are view-only
                ];
            });
        
        $total = TodayItem::where('user_id', Auth::id())
            ->whereDate('created_at', $date)
            ->sum('total_cost');
        
        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => number_format($total, 2),
            'count' => $items->count(),
            'date' => $date
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching items: ' . $e->getMessage()
        ], 500);
    }
}
}