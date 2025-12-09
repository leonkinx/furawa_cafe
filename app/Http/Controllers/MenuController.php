<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        // Stats untuk cards
        $stats = [
            'total_menus' => Menu::count(),
            'available_menus' => Menu::where('is_available', true)->count(),
            'out_of_stock' => Menu::where('stock', 0)->whereNotNull('stock')->count(),
            'best_sellers' => Menu::where('is_best_seller', true)->count(),
        ];
        
        // Query builder untuk menus
        $query = Menu::query();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        // Filter by availability
        if ($request->has('availability') && $request->availability != '') {
            if ($request->availability === 'available') {
                $query->where('is_available', true);
            } elseif ($request->availability === 'unavailable') {
                $query->where('is_available', false);
            } elseif ($request->availability === 'out_of_stock') {
                $query->where('stock', 0)->whereNotNull('stock');
            }
        }
        
        // Filter by best seller
        if ($request->has('best_seller') && $request->best_seller == '1') {
            $query->where('is_best_seller', true);
        }
        
        // Pagination (10 items per page)
        $menus = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.menus.index', compact('menus', 'stats'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        // Debug: lihat semua data yang dikirim
        \Log::info('=== MENU STORE REQUEST ===');
        \Log::info('All Request Data:', $request->all());
        \Log::info('Best Seller:', ['checked' => $request->has('is_best_seller'), 'value' => $request->is_best_seller]);
        \Log::info('Available:', ['checked' => $request->has('is_available'), 'value' => $request->is_available]);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:makanan,minuman,snack',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menu-images', 'public');
            }

            // Handle stock management
            $stock = $request->has('manage_stock') ? ($request->stock ?? 0) : null;

            // ✅ FIX: CHECKBOX HANDLING YANG BENAR
            $isBestSeller = $request->has('is_best_seller');
            $isAvailable = $request->has('is_available');

            \Log::info('Final checkbox values:', [
                'is_best_seller' => $isBestSeller,
                'is_available' => $isAvailable
            ]);

            // Create the menu
            $menu = Menu::create([
                'name' => $request->name,
                'description' => $request->description,
                'details' => $request->details,
                'price' => $request->price,
                'category' => $request->category,
                'stock' => $stock,
                'image' => $imagePath,
                'is_best_seller' => $isBestSeller,
                'is_available' => $isAvailable
            ]);

            \Log::info('Menu created successfully! ID: ' . $menu->id);

            return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan!');

        } catch (\Exception $e) {
            \Log::error('Error creating menu: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan menu: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        \Log::info('=== MENU UPDATE REQUEST ===');
        \Log::info('All Request Data:', $request->all());

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'details' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:makanan,minuman,snack',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $imagePath = $menu->image;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($menu->image) {
                    Storage::disk('public')->delete($menu->image);
                }
                $imagePath = $request->file('image')->store('menu-images', 'public');
            }

            // Handle stock management
            $stock = $request->has('manage_stock') ? ($request->stock ?? 0) : null;

            // ✅ FIX: CHECKBOX HANDLING YANG BENAR
            $isBestSeller = $request->has('is_best_seller');
            $isAvailable = $request->has('is_available');

            \Log::info('Final checkbox values for update:', [
                'is_best_seller' => $isBestSeller,
                'is_available' => $isAvailable
            ]);

            $menu->update([
                'name' => $request->name,
                'description' => $request->description,
                'details' => $request->details,
                'price' => $request->price,
                'category' => $request->category,
                'stock' => $stock,
                'image' => $imagePath,
                'is_best_seller' => $isBestSeller,
                'is_available' => $isAvailable
            ]);

            \Log::info('Menu updated successfully! ID: ' . $menu->id);

            return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diupdate!');

        } catch (\Exception $e) {
            \Log::error('Error updating menu: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengupdate menu: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Menu $menu)
    {
        try {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $menu->delete();

            return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.menus.index')->with('error', 'Gagal menghapus menu: ' . $e->getMessage());
        }
    }

    public function updateStock(Request $request, Menu $menu)
    {
        $request->validate([
            'action' => 'required|in:increase,decrease'
        ]);

        if ($menu->stock !== null) {
            $action = $request->action;
            
            if ($action === 'increase') {
                $menu->increment('stock');
                
                if ($menu->stock > 0 && !$menu->is_available) {
                    $menu->update(['is_available' => true]);
                }
                
            } elseif ($action === 'decrease') {
                if ($menu->stock > 0) {
                    $menu->decrement('stock');
                    
                    if ($menu->stock === 0) {
                        $menu->update(['is_available' => false]);
                    }
                }
            }
            
            return redirect()->route('admin.menus.index')->with('success', 'Stok berhasil diupdate!');
        }
        
        return redirect()->route('admin.menus.index')->with('error', 'Menu ini tidak dikelola stok!');
    }

    public function toggleAvailability(Request $request, Menu $menu)
    {
        try {
            $menu->update([
                'is_available' => !$menu->is_available
            ]);
            
            $status = $menu->is_available ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin.menus.index')->with('success', "Menu berhasil $status!");
        } catch (\Exception $e) {
            return redirect()->route('admin.menus.index')->with('error', 'Gagal mengubah status menu: ' . $e->getMessage());
        }
    }
}