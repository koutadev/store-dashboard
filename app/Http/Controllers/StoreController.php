<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with('area')->withCount('sales');

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $stores = $query->orderBy('code')->paginate(20)->withQueryString();
        $areas = Area::orderBy('name')->get();

        return view('stores.index', compact('stores', 'areas'));
    }

    public function create()
    {
        $areas = Area::orderBy('name')->get();
        return view('stores.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:stores,code',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Store::create($validated);

        return redirect()->route('stores.index')->with('success', '店舗を登録しました。');
    }

    public function edit(Store $store)
    {
        $areas = Area::orderBy('name')->get();
        return view('stores.edit', compact('store', 'areas'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:stores,code,' . $store->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $store->update($validated);

        return redirect()->route('stores.index')->with('success', '店舗を更新しました。');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return redirect()->route('stores.index')->with('success', '店舗を削除しました。');
    }
}