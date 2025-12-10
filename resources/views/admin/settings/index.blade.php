@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
                <p class="text-gray-600 mt-1">Kelola pengaturan service charge dan konfigurasi lainnya</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Service Charge Section -->
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-percentage text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Service Charge</h3>
                        <p class="text-sm text-gray-600">Atur persentase service charge yang akan ditambahkan ke setiap pesanan</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="service_charge_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                            Persentase Service Charge (%)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                id="service_charge_percentage"
                                name="service_charge_percentage" 
                                value="{{ old('service_charge_percentage', $serviceChargePercentage) }}"
                                min="0" 
                                max="100" 
                                step="0.01"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('service_charge_percentage') border-red-500 @enderror"
                                placeholder="Contoh: 10"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        @error('service_charge_percentage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">
                            Masukkan nilai 0-100. Contoh: 10 untuk service charge 10%
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <h4 class="font-medium text-gray-800 mb-2">Preview Perhitungan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">Rp 100.000</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service Charge (<span id="preview-percentage">{{ $serviceChargePercentage }}</span>%):</span>
                                <span class="font-medium" id="preview-amount">Rp {{ number_format(100000 * $serviceChargePercentage / 100, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-semibold">
                                <span>Total:</span>
                                <span id="preview-total">Rp {{ number_format(100000 + (100000 * $serviceChargePercentage / 100), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button 
                    type="submit" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceChargeInput = document.getElementById('service_charge_percentage');
    const previewPercentage = document.getElementById('preview-percentage');
    const previewAmount = document.getElementById('preview-amount');
    const previewTotal = document.getElementById('preview-total');
    
    function updatePreview() {
        const percentage = parseFloat(serviceChargeInput.value) || 0;
        const subtotal = 100000;
        const serviceCharge = subtotal * percentage / 100;
        const total = subtotal + serviceCharge;
        
        previewPercentage.textContent = percentage;
        previewAmount.textContent = 'Rp ' + serviceCharge.toLocaleString('id-ID');
        previewTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
    
    serviceChargeInput.addEventListener('input', updatePreview);
});
</script>
@endsection