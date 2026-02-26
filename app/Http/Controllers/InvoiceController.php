<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceCustomer;
use App\Models\Advanced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceCustomer::with(['invoices', 'advances']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(10);

        foreach ($customers as $customer) {
            $customer->total_final_amount = (float) ($customer->final_amount ?? 0);
            $customer->total_advance_amount = (float) $customer->advances->sum('advance_amount');
            $customer->due_amount = $customer->total_final_amount - $customer->total_advance_amount;
        }

        return view('user.invoice.index', compact('customers'));
    }

    public function create()
    {
        return view('user.invoice.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'items' => 'required|array|min:1',
        'items.*' => 'string',
        'rate' => 'required|array',
        'qty' => 'required|array',
        'item_discount' => 'required|array',
        'total_amount' => 'required|numeric|min:0',
        'final_discount' => 'nullable|numeric|min:0|max:100',
        'final_amount' => 'required|numeric|min:0',
        'advance_amount' => 'nullable|numeric|min:0',
        'advance_date' => 'nullable|date',
        'customer_name' => 'nullable|string|max:255',
        'phone_number' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'location' => 'nullable|string|max:255',
        'invoice_date' => 'nullable|date', // Added invoice_date validation
    ]);

    try {
        DB::beginTransaction();

        // Get the selected items
        $selectedItems = $request->items;
        
        // Calculate total amount from selected items only
        $calculatedTotalAmount = 0;
        $invoiceItems = [];

        foreach ($selectedItems as $itemName) {
            // Only process if this item is selected and has data
            if (isset($request->rate[$itemName]) && isset($request->qty[$itemName])) {
                $rate = (float) ($request->rate[$itemName] ?? 0);
                $qty = (float) ($request->qty[$itemName] ?? 1);
                $itemDiscount = (float) ($request->item_discount[$itemName] ?? 0);
                
                // Calculate item amount: (rate * qty) - ((rate * qty) * item_discount/100)
                $subTotal = $rate * $qty;
                $itemDiscountAmount = $subTotal * ($itemDiscount / 100);
                $itemAmount = round($subTotal - $itemDiscountAmount, 2);
                
                // Add to total
                $calculatedTotalAmount += $itemAmount;
                
                // Store for later use
                $invoiceItems[] = [
                    'item_name' => $itemName,
                    'rate' => $rate,
                    'qty' => $qty,
                    'item_discount' => $itemDiscount,
                    'amount' => $itemAmount,
                    'final_amount' => $itemAmount,
                ];
            }
        }

        // Use either the calculated total or the user-provided total amount
        $totalAmount = $request->total_amount > 0 ? (float) $request->total_amount : $calculatedTotalAmount;
        $finalDiscount = (float) ($request->final_discount ?? 0);

        // Calculate final amount after discount
        $discountAmount = $totalAmount * ($finalDiscount / 100);
        $finalAmount = round($totalAmount - $discountAmount, 2);

        // Create customer record
        $customer = InvoiceCustomer::create([
            'name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'location' => $request->location,
            'total_amount' => $totalAmount,
            'final_discount' => $finalDiscount,
            'final_amount' => $finalAmount,
        ]);

        // Persist individual invoice lines (only selected items)
        foreach ($invoiceItems as $itemData) {
            Invoice::create([
                'invoice_customer_id' => $customer->id,
                'item_name' => $itemData['item_name'],
                'rate' => $itemData['rate'],
                'qty' => $itemData['qty'],
                'item_discount' => $itemData['item_discount'],
                'amount' => $itemData['amount'],
                'final_amount' => $itemData['final_amount'],
                'date' => $request->invoice_date ?? now(), // Added invoice date
                // invoice_number will be auto-generated by the model boot method
            ]);
        }

        // Advance payment (optional)
        if ($request->filled('advance_amount') && (float) $request->advance_amount > 0) {
            $advanceAmount = (float) $request->advance_amount;
            $dueBalance = $finalAmount - $advanceAmount;

            Advanced::create([
                'invoice_id' => null,
                'invoice_customer_id' => $customer->id,
                'advance_amount' => $advanceAmount,
                'due_balance' => $dueBalance >= 0 ? $dueBalance : 0,
                'date' => $request->advance_date ?? now(),
            ]);
        }

        DB::commit();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Invoice creation failed: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Failed to create invoice. Please try again.')
            ->withInput();
    }
}

    public function show($id)
    {
        $customer = InvoiceCustomer::with(['invoices', 'advances'])->findOrFail($id);

        $customer->total_final_amount = (float) ($customer->final_amount ?? 0);
        $customer->total_advance_amount = (float) $customer->advances->sum('advance_amount');
        $customer->due_amount = $customer->total_final_amount - $customer->total_advance_amount;

        return view('user.invoice.show', compact('customer'));
    }

    public function print($id)
    {
        $customer = InvoiceCustomer::with(['invoices', 'advances'])->findOrFail($id);

        $customer->total_final_amount = (float) ($customer->final_amount ?? 0);
        $customer->total_advance_amount = (float) $customer->advances->sum('advance_amount');
        $customer->due_amount = $customer->total_final_amount - $customer->total_advance_amount;

        return view('user.invoice.print', compact('customer'));
    }

    public function addAdvance(Request $request, $id)
    {
        $request->validate([
            'advance_amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $customer = InvoiceCustomer::findOrFail($id);

            $totalFinalAmount = (float) $customer->final_amount;
            $totalAdvancePaid = (float) Advanced::where('invoice_customer_id', $id)->sum('advance_amount');
            $currentDueBalance = $totalFinalAmount - $totalAdvancePaid;

            if ((float) $request->advance_amount > $currentDueBalance) {
                return redirect()->back()
                    ->with('error', 'Advance amount cannot exceed the due balance of LKR ' . number_format($currentDueBalance, 2) . '!')
                    ->withInput();
            }

            $newDueBalance = $currentDueBalance - (float) $request->advance_amount;

            Advanced::create([
                'invoice_id' => null,
                'invoice_customer_id' => $customer->id,
                'advance_amount' => $request->advance_amount,
                'due_balance' => $newDueBalance >= 0 ? $newDueBalance : 0,
                'date' => $request->date,
            ]);

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Advance payment of LKR ' . number_format($request->advance_amount, 2) . ' added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add advance failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to add advance payment. Please try again.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $customer = InvoiceCustomer::findOrFail($id);
            Advanced::where('invoice_customer_id', $id)->delete();
            Invoice::where('invoice_customer_id', $id)->delete();
            $customer->delete();

            DB::commit();

            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete invoice failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete invoice. Please try again.');
        }
    }

    public function getCustomerDetails($id)
    {
        try {
            $customer = InvoiceCustomer::with('invoices')->findOrFail($id);

            $totalFinalAmount = (float) ($customer->final_amount ?? 0);
            $totalAdvanceAmount = (float) Advanced::where('invoice_customer_id', $id)->sum('advance_amount');
            $dueBalance = $totalFinalAmount - $totalAdvanceAmount;

            return response()->json([
                'success' => true,
                'customer_name' => $customer->name,
                'total_final_amount' => $totalFinalAmount,
                'total_advance_amount' => $totalAdvanceAmount,
                'due_balance' => $dueBalance >= 0 ? $dueBalance : 0,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
        }
    }
}