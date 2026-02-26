<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\FabricCal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $search = request('search');

        $customers = Customer::when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('user.customer.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:20',
            ]);

            Customer::create($validated);

            return redirect()->route('user.customer.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Validation failed. Please check the form fields.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $fabricCalculations = FabricCal::where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals for this customer
        $totals = [
            'stick' => $fabricCalculations->sum('stick'),
            'one_rali' => $fabricCalculations->sum('one_rali'),
            'two_rali' => $fabricCalculations->sum('two_rali'),
            'tree_rali' => $fabricCalculations->sum('tree_rali'),
            'four_rali' => $fabricCalculations->sum('four_rali'),
            'ilets' => $fabricCalculations->sum('ilets'),
            'sum_one_four' => $fabricCalculations->sum('sum_one_four'),
            'sum_two_tree' => $fabricCalculations->sum('sum_two_tree'),
        ];

        return view('user.customer.show', compact('customer', 'fabricCalculations', 'totals'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($customer);
        }

        return view('user.customer.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'nullable|string|max:20',
            ]);

            $customer->update($validated);

            return redirect()->route('user.customer.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the customer: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customerName = $customer->name;
            $customer->delete();

            return redirect()->route('user.customer.index')
                ->with('success', 'Customer (' . $customerName . ') deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.customer.index')
                ->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    /**
     * Fabric Cal methods
     */
    public function fabricCreate($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        return view('user.customer.fabric-create', compact('customer'));
    }

    public function fabricStore(Request $request, $customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);

            $validated = $request->validate([
                'stick' => 'nullable|numeric|min:0',
                'one_rali' => 'nullable|numeric|min:0',
                'two_rali' => 'nullable|numeric|min:0',
                'tree_rali' => 'nullable|numeric|min:0',
                'four_rali' => 'nullable|numeric|min:0',
            ]);

            // Stick value (no multiplication needed)
            $stick = isset($validated['stick']) && $validated['stick'] !== '' ? $validated['stick'] : null;

            // Convert input values (multiply by 34 - updated from 36)
            $oneRali = isset($validated['one_rali']) && $validated['one_rali'] !== '' ? $validated['one_rali'] * 34 : null;
            $twoRali = isset($validated['two_rali']) && $validated['two_rali'] !== '' ? $validated['two_rali'] * 34 : null;
            $treeRali = isset($validated['tree_rali']) && $validated['tree_rali'] !== '' ? $validated['tree_rali'] * 34 : null;
            $fourRali = isset($validated['four_rali']) && $validated['four_rali'] !== '' ? $validated['four_rali'] * 34 : null;

            // Calculate ilets = sum of all ralis divided by 17
            $raliValues = array_filter([$oneRali, $twoRali, $treeRali, $fourRali]);
            $raliSum = array_sum($raliValues);
            $ilets = $raliSum > 0 ? $raliSum / 17 : null;

            // Get existing calculations for this customer to calculate sums
            $existingCalculations = FabricCal::where('customer_id', $customerId)->get();
            
            // Calculate sum_one_four (all one_rali + all four_rali for this customer)
            $existingOneRaliSum = $existingCalculations->sum('one_rali');
            $existingFourRaliSum = $existingCalculations->sum('four_rali');
            $sumOneFour = $existingOneRaliSum + $existingFourRaliSum + ($oneRali ?? 0) + ($fourRali ?? 0);

            // Calculate sum_two_tree (all two_rali + all tree_rali for this customer)
            $existingTwoRaliSum = $existingCalculations->sum('two_rali');
            $existingTreeRaliSum = $existingCalculations->sum('tree_rali');
            $sumTwoTree = $existingTwoRaliSum + $existingTreeRaliSum + ($twoRali ?? 0) + ($treeRali ?? 0);

            // Create fabric calculation
            FabricCal::create([
                'customer_id' => $customerId,
                'stick' => $stick,
                'one_rali' => $oneRali,
                'two_rali' => $twoRali,
                'tree_rali' => $treeRali,
                'four_rali' => $fourRali,
                'ilets' => $ilets,
                'sum_one_four' => $sumOneFour,
                'sum_two_tree' => $sumTwoTree,
            ]);

            // Update all existing records with new sums
            FabricCal::where('customer_id', $customerId)->update([
                'sum_one_four' => $sumOneFour,
                'sum_two_tree' => $sumTwoTree,
            ]);

            return redirect()->route('user.customer.show', $customerId)
                ->with('success', 'Fabric calculation added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function fabricEdit($customerId, $fabricId)
    {
        $customer = Customer::findOrFail($customerId);
        $fabricCal = FabricCal::where('id', $fabricId)->where('customer_id', $customerId)->firstOrFail();

        if (request()->ajax()) {
            // Return with original input values (divided by 34 for display - updated from 36)
            $fabricCal->display_one_rali = $fabricCal->one_rali ? $fabricCal->one_rali / 34 : null;
            $fabricCal->display_two_rali = $fabricCal->two_rali ? $fabricCal->two_rali / 34 : null;
            $fabricCal->display_tree_rali = $fabricCal->tree_rali ? $fabricCal->tree_rali / 34 : null;
            $fabricCal->display_four_rali = $fabricCal->four_rali ? $fabricCal->four_rali / 34 : null;
            // Stick is returned as is (no division needed)
            $fabricCal->display_stick = $fabricCal->stick;
            return response()->json($fabricCal);
        }

        return view('user.customer.fabric-edit', compact('customer', 'fabricCal'));
    }

    public function fabricUpdate(Request $request, $customerId, $fabricId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $fabricCal = FabricCal::where('id', $fabricId)->where('customer_id', $customerId)->firstOrFail();

            $validated = $request->validate([
                'stick' => 'nullable|numeric|min:0',
                'one_rali' => 'nullable|numeric|min:0',
                'two_rali' => 'nullable|numeric|min:0',
                'tree_rali' => 'nullable|numeric|min:0',
                'four_rali' => 'nullable|numeric|min:0',
            ]);

            // Stick value (no multiplication)
            $stick = isset($validated['stick']) && $validated['stick'] !== '' ? $validated['stick'] : null;

            // Convert input values (multiply by 34 - updated from 36)
            $oneRali = isset($validated['one_rali']) && $validated['one_rali'] !== '' ? $validated['one_rali'] * 34 : null;
            $twoRali = isset($validated['two_rali']) && $validated['two_rali'] !== '' ? $validated['two_rali'] * 34 : null;
            $treeRali = isset($validated['tree_rali']) && $validated['tree_rali'] !== '' ? $validated['tree_rali'] * 34 : null;
            $fourRali = isset($validated['four_rali']) && $validated['four_rali'] !== '' ? $validated['four_rali'] * 34 : null;

            // Update the record
            $fabricCal->stick = $stick;
            $fabricCal->one_rali = $oneRali;
            $fabricCal->two_rali = $twoRali;
            $fabricCal->tree_rali = $treeRali;
            $fabricCal->four_rali = $fourRali;

            // Calculate ilets for this record
            $raliValues = array_filter([$oneRali, $twoRali, $treeRali, $fourRali]);
            $raliSum = array_sum($raliValues);
            $fabricCal->ilets = $raliSum > 0 ? $raliSum / 17 : null;
            
            $fabricCal->save();

            // Recalculate all sums for this customer
            $this->recalculateCustomerSums($customerId);

            return redirect()->route('user.customer.show', $customerId)
                ->with('success', 'Fabric calculation updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function fabricDestroy($customerId, $fabricId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            $fabricCal = FabricCal::where('id', $fabricId)->where('customer_id', $customerId)->firstOrFail();
            
            $fabricCal->delete();

            // Recalculate all sums for this customer
            $this->recalculateCustomerSums($customerId);

            return redirect()->route('user.customer.show', $customerId)
                ->with('success', 'Fabric calculation deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.customer.show', $customerId)
                ->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate all sums for a customer
     */
    private function recalculateCustomerSums($customerId)
    {
        $calculations = FabricCal::where('customer_id', $customerId)->get();
        
        $totalOneFour = $calculations->sum('one_rali') + $calculations->sum('four_rali');
        $totalTwoTree = $calculations->sum('two_rali') + $calculations->sum('tree_rali');

        // Update all records with new totals
        FabricCal::where('customer_id', $customerId)->update([
            'sum_one_four' => $totalOneFour,
            'sum_two_tree' => $totalTwoTree,
        ]);
    }

    public function storeMultiple(Request $request, $customerId)
{
    $request->validate([
        'rows' => 'required|array',
        'rows.*.stick' => 'nullable|numeric|min:0',
        'rows.*.one_rali' => 'nullable|numeric|min:0',
        'rows.*.two_rali' => 'nullable|numeric|min:0',
        'rows.*.tree_rali' => 'nullable|numeric|min:0',
        'rows.*.four_rali' => 'nullable|numeric|min:0',
    ]);

    $customer = Customer::findOrFail($customerId);
    $savedCount = 0;

    foreach ($request->rows as $row) {
        // Skip empty rows
        if (empty($row['stick']) && empty($row['one_rali']) && empty($row['two_rali']) && 
            empty($row['tree_rali']) && empty($row['four_rali'])) {
            continue;
        }

        // Calculate the saved values (multiply by 34)
        $oneRaliSaved = isset($row['one_rali']) ? floatval($row['one_rali']) * 34 : 0;
        $twoRaliSaved = isset($row['two_rali']) ? floatval($row['two_rali']) * 34 : 0;
        $treeRaliSaved = isset($row['tree_rali']) ? floatval($row['tree_rali']) * 34 : 0;
        $fourRaliSaved = isset($row['four_rali']) ? floatval($row['four_rali']) * 34 : 0;

        // Calculate ilets
        $totalRali = $oneRaliSaved + $twoRaliSaved + $treeRaliSaved + $fourRaliSaved;
        $ilets = $totalRali > 0 ? $totalRali / 17 : 0;

        // Calculate sums
        $sumOneFour = $oneRaliSaved + $fourRaliSaved;
        $sumTwoTree = $twoRaliSaved + $treeRaliSaved;

        // Create fabric calculation
        $customer->fabricCalculations()->create([
            'stick' => $row['stick'] ?? 0,
            'one_rali' => $oneRaliSaved,
            'two_rali' => $twoRaliSaved,
            'tree_rali' => $treeRaliSaved,
            'four_rali' => $fourRaliSaved,
            'ilets' => $ilets,
            'sum_one_four' => $sumOneFour,
            'sum_two_tree' => $sumTwoTree,
        ]);

        $savedCount++;
    }

    if ($savedCount > 0) {
        return redirect()->route('user.customer.show', $customerId)
            ->with('success', $savedCount . ' fabric calculation(s) added successfully!');
    } else {
        return redirect()->route('user.customer.show', $customerId)
            ->with('error', 'No fabric calculations were added.');
    }
}
}