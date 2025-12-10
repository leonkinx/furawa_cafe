<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $serviceChargePercentage = Setting::getServiceChargePercentage();
        
        return view('admin.settings.index', compact('serviceChargePercentage'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'service_charge_percentage' => 'required|numeric|min:0|max:100'
        ]);
        
        Setting::setServiceChargePercentage($request->service_charge_percentage);
        
        return redirect()->back()->with('success', 'Service charge berhasil diupdate!');
    }
    
    /**
     * API endpoint untuk mendapatkan service charge percentage
     */
    public function getServiceCharge()
    {
        return response()->json([
            'service_charge_percentage' => Setting::getServiceChargePercentage()
        ]);
    }
}