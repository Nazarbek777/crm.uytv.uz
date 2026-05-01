@extends('layouts.main')

@section('title', 'Savdolar')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Savdolar</h2>
        <p class="text-gray-600">Barcha savdolar ro'yxati</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
        <i class="fas fa-plus mr-2"></i>Yangi Savdo
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4 flex-wrap">
        <select name="type" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
            <option value="">Barcha turlar</option>
            <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sotuv</option>
            <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>Ijara</option>
        </select>
    </form>
</div>

<!-- Sales Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uy</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mijoz</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tur</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sana</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amallar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($sales as $index => $sale)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ $sale->property->title }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $sale->client->name ?? '-' }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ number_format($sale->price, 0, '.', ' ') }} so'm</td>
                <td class="px-6 py-4">
                    @if($sale->type == 'sale')
                        <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Sotuv</span>
                    @else
                        <span class="px-2 py-1 bg-orange-100 text-orange-600 rounded-full text-sm">Ijara</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $sale->sale_date->format('d.m.Y') }}</td>
                <td class="px-6 py-4">
                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Rostan o\'chirish?')" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Savdolar yo'q</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-semibold mb-4">Yangi Savdo</h3>
        <form action="{{ route('sales.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Uy *</label>
                <select name="property_id" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Tanlang</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}">{{ $property->title }} - {{ number_format($property->price, 0, '.', ' ') }} so'm</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mijoz</label>
                <select name="client_id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Tanlang</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Narx (so'm) *</label>
                    <input type="number" name="price" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tur *</label>
                    <select name="type" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="sale">Sotuv</option>
                        <option value="rent">Ijara</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sana *</label>
                <input type="date" name="sale_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Izoh</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Bekor</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Saqlash</button>
            </div>
        </form>
    </div>
</div>
@endsection