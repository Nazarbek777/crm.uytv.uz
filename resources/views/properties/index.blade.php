@extends('layouts.main')

@section('title', 'Uylar')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Uylar</h2>
        <p class="text-gray-600">Barcha uylar ro'yxati</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i>Yangi Uy
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4 flex-wrap">
        <select name="status" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
            <option value="">Barcha holatlar</option>
            <option value="free" {{ request('status') == 'free' ? 'selected' : '' }}>Bo'sh</option>
            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sotilgan</option>
            <option value="rent" {{ request('status') == 'rent' ? 'selected' : '' }}>Ijara</option>
        </select>
        <select name="investor_id" onchange="this.form.submit()" class="px-3 py-2 border rounded-lg">
            <option value="">Barcha investorlar</option>
            @foreach($investors as $investor)
                <option value="{{ $investor->id }}" {{ request('investor_id') == $investor->id ? 'selected' : '' }}>{{ $investor->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Properties Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Manzil</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Narx</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Holat</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Investor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amallar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($properties as $index => $property)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ $property->title }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $property->address }}</td>
                <td class="px-6 py-4 font-medium text-gray-800">{{ number_format($property->price, 0, '.', ' ') }} so'm</td>
                <td class="px-6 py-4">
                    @if($property->status == 'free')
                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-sm">Bo'sh</span>
                    @elseif($property->status == 'sold')
                        <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-full text-sm">Sotilgan</span>
                    @else
                        <span class="px-2 py-1 bg-orange-100 text-orange-600 rounded-full text-sm">Ijara</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $property->investor->name ?? '-' }}</td>
                <td class="px-6 py-4">
                    <button onclick="editProperty({{ $property->id }}, '{{ $property->title }}', '{{ $property->address }}', {{ $property->price }}, '{{ $property->status }}', {{ $property->rooms ?? 0 }}, {{ $property->floor ?? 0 }}, {{ $property->total_floors ?? 0 }}, {{ $property->area ?? 0 }}, '{{ $property->description }}', {{ $property->investor_id ?? 'null' }})" class="text-blue-600 hover:text-blue-800 mr-3">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('properties.destroy', $property->id) }}" method="POST" class="inline">
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
                <td colspan="7" class="px-6 py-8 text-center text-gray-500">Uylar yo'q</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">Yangi Uy</h3>
        <form action="{{ route('properties.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Investor</label>
                    <select name="investor_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Tanlang</option>
                        @foreach($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Manzil *</label>
                <input type="text" name="address" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Narx (so'm) *</label>
                    <input type="number" name="price" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Holat *</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="free">Bo'sh</option>
                        <option value="sold">Sotilgan</option>
                        <option value="rent">Ijara</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xonalar</label>
                    <input type="number" name="rooms" min="1" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qavat</label>
                    <input type="number" name="floor" min="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Umumiy qavat</label>
                    <input type="number" name="total_floors" min="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Maydon (m²)</label>
                <input type="number" name="area" min="0" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Izoh</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Bekor</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-semibold mb-4">Uyni tahrirlash</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="title" id="editTitle" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Investor</label>
                    <select name="investor_id" id="editInvestor" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Tanlang</option>
                        @foreach($investors as $investor)
                            <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Manzil *</label>
                <input type="text" name="address" id="editAddress" required class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Narx (so'm) *</label>
                    <input type="number" name="price" id="editPrice" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Holat *</label>
                    <select name="status" id="editStatus" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="free">Bo'sh</option>
                        <option value="sold">Sotilgan</option>
                        <option value="rent">Ijara</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xonalar</label>
                    <input type="number" name="rooms" id="editRooms" min="1" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qavat</label>
                    <input type="number" name="floor" id="editFloor" min="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Umumiy qavat</label>
                    <input type="number" name="total_floors" id="editTotalFloors" min="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Maydon (m²)</label>
                <input type="number" name="area" id="editArea" min="0" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Izoh</label>
                <textarea name="description" id="editDescription" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Bekor</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<script>
function editProperty(id, title, address, price, status, rooms, floor, totalFloors, area, description, investorId) {
    document.getElementById('editForm').action = '/properties/' + id;
    document.getElementById('editTitle').value = title || '';
    document.getElementById('editAddress').value = address || '';
    document.getElementById('editPrice').value = price || 0;
    document.getElementById('editStatus').value = status || 'free';
    document.getElementById('editRooms').value = rooms || 0;
    document.getElementById('editFloor').value = floor || 0;
    document.getElementById('editTotalFloors').value = totalFloors || 0;
    document.getElementById('editArea').value = area || 0;
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editInvestor').value = investorId || '';
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection