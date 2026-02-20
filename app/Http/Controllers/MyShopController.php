<?php

namespace App\Http\Controllers;

use App\Models\MyShopDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyShopController extends Controller
{
    public function index()
    {
        $shop = MyShopDetail::where('user_id', Auth::id())->first();
        $hasShop = $shop !== null;

        return view('user.myshop.index', compact('shop', 'hasShop'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'hotline' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'condition_1' => 'nullable|string',
            'condition_2' => 'nullable|string',
            'condition_3' => 'nullable|string',
        ]);

        $data = $request->except('logo_image');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('logo_image')) {
            $path = $request->file('logo_image')->store('shop_logos', 'public');
            $data['logo_image'] = $path;
        }

        MyShopDetail::create($data);

        return redirect()->route('user.myshop.index')
            ->with('success', 'Shop details created successfully.');
    }

    public function show($id)
    {
        try {
            $shop = MyShopDetail::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'id' => $shop->id,
                    'shop_name' => $shop->shop_name,
                    'description' => $shop->description,
                    'address' => $shop->address,
                    'hotline' => $shop->hotline,
                    'email' => $shop->email,
                    'logo_image' => $shop->logo_image,
                    'condition_1' => $shop->condition_1,
                    'condition_2' => $shop->condition_2,
                    'condition_3' => $shop->condition_3,
                ]);
            }

            return redirect()->route('user.myshop.index');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'error' => 'Shop details not found'
                ], 404);
            }

            return redirect()->route('user.myshop.index')
                ->with('error', 'Shop details not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'hotline' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'logo_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'condition_1' => 'nullable|string',
            'condition_2' => 'nullable|string',
            'condition_3' => 'nullable|string',
        ]);

        $shop = MyShopDetail::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $data = $request->except('logo_image');

        if ($request->hasFile('logo_image')) {
            if ($shop->logo_image) {
                Storage::disk('public')->delete($shop->logo_image);
            }

            $path = $request->file('logo_image')->store('shop_logos', 'public');
            $data['logo_image'] = $path;
        }

        $shop->update($data);

        return redirect()->route('user.myshop.index')
            ->with('success', 'Shop details updated successfully.');
    }
}
