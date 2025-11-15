@extends('layouts.admin')

@section('breadcrumbs')
    <nav class="flex items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="hover:underline">Admin</a>
        <span>/</span>
        <span class="text-white">Kelas</span>
    </nav>
@endsection

@section('header-right')
    <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-white/10 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white/30 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
        <span>Tambah</span>
    </button>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <section aria-labelledby="master-kelas">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <h1 id="master-kelas" class="text-2xl lg:text-3xl font-bold text-gray-900">Master Kelas</h1>
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="openAddModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16M4 12h16"/></svg>
                    <span>Tambah Kelas</span>
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
            <form method="GET" action="{{ route('admin.classes') }}" class="flex items-center gap-4">
                <div class="relative flex-1 sm:max-w-md">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari kelas..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/30 transition-colors">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.classes') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kode</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tingkat</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Program</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kapasitas</th>
                            <th class="px-4 md:px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($classes as $kelas)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $kelas->id }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $kelas->name }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kelas->code ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kelas->level ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kelas->program ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kelas->capacity ?? '-' }}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick='openEditModal({{ $kelas->id }}, @json($kelas->name), @json($kelas->code ?? ""), @json($kelas->level ?? ""), @json($kelas->program ?? ""), @json($kelas->capacity ?? ""), @json($kelas->description ?? ""))' class="inline-flex items-center px-2 py-1.5 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487l3.651 3.651m-2.514-4.39l-9.803 9.804a4.5 4.5 0 00-1.253 2.303l-.51 2.553 2.553-.51a4.5 4.5 0 002.303-1.253l9.803-9.803"/></svg>
                                        </button>
                                        <button onclick='openDeleteModal({{ $kelas->id }}, @json($kelas->name))' class="inline-flex items-center px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-1 0l-1 12H8L7 7m5-4a1 1 0 011 1v2H9V4a1 1 0 011-1h2z"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 md:px-6 py-12 text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z"/>
                                    </svg>
                                    <p class="text-sm">Tidak ada kelas ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($classes->hasPages())
                <div class="px-4 md:px-6 py-4 border-t border-gray-200">
                    {{ $classes->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Modal: Add Class -->
    <div id="addModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeAddModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('admin.classes.store') }}">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Kelas</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: X IPA 1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode (opsional)</label>
                                <input type="text" name="code" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: XIPA1">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat (opsional)</label>
                                    <select name="level" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Tingkat</option>
                                        <optgroup label="SD">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </optgroup>
                                        <optgroup label="SMP (Angka)">
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                        </optgroup>
                                        <optgroup label="SMP (Romawi)">
                                            <option value="VII">VII</option>
                                            <option value="VIII">VIII</option>
                                            <option value="IX">IX</option>
                                        </optgroup>
                                        <optgroup label="SMA (Angka)">
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </optgroup>
                                        <optgroup label="SMA (Romawi)">
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                        </optgroup>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Isi jika diperlukan; bisa dikosongkan.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Program (opsional)</label>
                                    <select name="program" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Program</option>
                                        <option value="IPA">IPA</option>
                                        <option value="IPS">IPS</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (opsional)</label>
                                <input type="number" name="capacity" min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jumlah maksimal siswa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                                <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Deskripsi kelas..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-sm font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto">
                            Simpan
                        </button>
                        <button type="button" onclick="closeAddModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Class -->
    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeEditModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Kelas</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="edit_name" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: X IPA 1">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kode (opsional)</label>
                                <input type="text" name="code" id="edit_code" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Contoh: XIPA1">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat (opsional)</label>
                                    <select name="level" id="edit_level" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Tingkat</option>
                                        <optgroup label="SD">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                        </optgroup>
                                        <optgroup label="SMP (Angka)">
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                        </optgroup>
                                        <optgroup label="SMP (Romawi)">
                                            <option value="VII">VII</option>
                                            <option value="VIII">VIII</option>
                                            <option value="IX">IX</option>
                                        </optgroup>
                                        <optgroup label="SMA (Angka)">
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </optgroup>
                                        <optgroup label="SMA (Romawi)">
                                            <option value="X">X</option>
                                            <option value="XI">XI</option>
                                            <option value="XII">XII</option>
                                        </optgroup>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Isi jika diperlukan; bisa dikosongkan.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Program (opsional)</label>
                                    <select name="program" id="edit_program" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30">
                                        <option value="">Pilih Program</option>
                                        <option value="IPA">IPA</option>
                                        <option value="IPS">IPS</option>
                                        <option value="Umum">Umum</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (opsional)</label>
                                <input type="number" name="capacity" id="edit_capacity" min="1" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Jumlah maksimal siswa">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                                <textarea name="description" id="edit_description" rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Deskripsi kelas..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-primary text-white text-sm font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto">
                            Update
                        </button>
                        <button type="button" onclick="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeDeleteModal()"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900" id="deleteClassName">Hapus Kelas?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus kelas ini? Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" id="deleteForm" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto">
                            Hapus
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add Modal
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        // Edit Modal
        function openEditModal(id, name, code, level, program, capacity, description) {
            document.getElementById('editForm').action = `{{ url('admin/classes') }}/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_code').value = code || '';
            document.getElementById('edit_level').value = level || '';
            document.getElementById('edit_program').value = program || '';
            document.getElementById('edit_capacity').value = capacity || '';
            document.getElementById('edit_description').value = description || '';
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Delete Modal
        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = `{{ url('admin/classes') }}/${id}`;
            document.getElementById('deleteClassName').textContent = `Hapus kelas "${name}"?`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
@endsection