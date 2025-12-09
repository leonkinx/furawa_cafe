@extends('layouts.admin')

@section('title', 'Manajemen Akun Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 uppercase tracking-wide">Manajemen Akun Admin</h1>
        <p class="text-gray-600 mt-2">Kelola akun admin untuk login</p>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="group relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 rounded-full -mr-16 -mt-16 opacity-20 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-800">{{ $users->total() }}</p>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Admin</h3>
                <div class="mt-2 flex items-center text-xs text-blue-700">
                    <i class="fas fa-user-shield text-blue-500 mr-2"></i>
                    <span>Akun terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    <!-- Toolbar -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Total: <span class="font-semibold">{{ $users->total() }}</span> akun
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i>Tambah Admin
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Terdaftar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $index => $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $users->firstItem() + $index }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                <span class="text-xs text-blue-600">(Anda)</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="text-blue-600 hover:text-blue-700" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-700" 
                                        title="Hapus"
                                        onclick="return confirm('Hapus akun {{ $user->name }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center text-gray-400">
                            <i class="fas fa-users text-3xl mb-2"></i>
                            <p class="text-sm text-gray-500">Belum ada akun admin</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="mt-4 flex items-center justify-between text-sm">
        <div class="text-gray-600">
            Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }}
        </div>
        
        <div class="flex gap-1">
            @if($users->onFirstPage())
            <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                <i class="fas fa-chevron-left"></i>
            </span>
            @else
            <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </a>
            @endif

            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                <span class="px-3 py-1.5 bg-blue-600 text-white rounded font-medium">{{ $page }}</span>
                @else
                <a href="{{ $url }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">{{ $page }}</a>
                @endif
            @endforeach

            @if($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </a>
            @else
            <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded cursor-not-allowed">
                <i class="fas fa-chevron-right"></i>
            </span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
