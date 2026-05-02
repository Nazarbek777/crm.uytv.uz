@extends('layouts.main')

@section('title', 'Mijozlar')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Mijozlar</h2>
        <p class="text-gray-600">Barcha mijozlar ro'yxati</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
        <i class="fas fa-plus mr-2"></i>Yangi Mijoz
    </button>
</div>

<!-- Search Form -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ism, telefon yoki email bo'yicha qidirish..." class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-search mr-2"></i>Qidirish
        </button>
        @if(request('search'))
            <a href="{{ route('clients.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Tozalash
            </a>
        @endif
    </form>
</div>

<!-- Clients Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <table class="w-full">
        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ism</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Telefon</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Izoh</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Amallar</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($clients as $index => $client)
            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-colors duration-200">
                <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-semibold text-gray-800">{{ $client->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $client->phone ?? '-' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $client->email ?? '-' }}</td>
                <td class="px-6 py-4 text-gray-500 text-sm">{{ Str::limit($client->notes, 50) }}</td>
                <td class="px-6 py-4">
                    <button onclick="editClient({{ $client->id }}, '{{ $client->name }}', '{{ $client->phone }}', '{{ $client->email }}', '{{ $client->notes }}')" class="text-blue-600 hover:text-blue-800 mr-3 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Rostan o\'chirish?')" class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                    <p>Mijozlar yo'q</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $clients->appends(request()->query())->links() }}
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 animate-fade-in">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 animate-modal-in">
        <h3 class="text-2xl font-bold mb-6 text-gray-800">Yangi Mijoz</h3>
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ism *</label>
                <input type="text" name="name" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Telefon</label>
                <input type="text" name="phone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Izoh</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all"></textarea>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold">Bekor</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all font-semibold shadow-lg">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 animate-fade-in">
    <div class="bg-white rounded-2xl p-8 w-full max-w-md shadow-2xl transform scale-95 animate-modal-in">
        <h3 class="text-2xl font-bold mb-6 text-gray-800">Mijozni tahrirlash</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Ism *</label>
                <input type="text" name="name" id="editName" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Telefon</label>
                <input type="text" name="phone" id="editPhone" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" id="editEmail" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Izoh</label>
                <textarea name="notes" id="editNotes" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-500 transition-all"></textarea>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold">Bekor</button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all font-semibold shadow-lg">Saqlash</button>
            </div>
        </form>
    </div>
</div>

<script>
function editClient(id, name, phone, email, notes) {
    document.getElementById('editForm').action = '/clients/' + id;
    document.getElementById('editName').value = name || '';
    document.getElementById('editPhone').value = phone || '';
    document.getElementById('editEmail').value = email || '';
    document.getElementById('editNotes').value = notes || '';
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endsection