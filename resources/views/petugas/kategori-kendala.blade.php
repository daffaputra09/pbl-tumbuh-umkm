@extends('layouts.app-shell')

@section('title', 'Kategori Kendala — TUMBUH UMKM')

@section('content')
    <div class="space-y-6">
        @if(session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                <x-hugeicon name="CheckmarkCircle02Icon" :size="20" class="text-emerald-600 shrink-0" />
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <x-hugeicon name="AlertCircleIcon" :size="20" class="text-rose-600 shrink-0" />
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <div class="font-semibold mb-1">Terdapat beberapa kesalahan pengisian formulir:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand transition-colors mb-2">
                    <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                    Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Kategori Kendala</h1>
                <p class="mt-1 text-sm text-slate-600 max-w-3xl">
                    Kelola master data kategori kendala UMKM yang digunakan oleh bank soal asesmen, penghitungan skor, dan rekomendasi program bantuan.
                </p>
            </div>
            <div class="shrink-0">
                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Total Kategori</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-600">
                        <x-hugeicon name="Tag01Icon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-ink">{{ $categories->count() }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Seluruh kategori terdaftar</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Kategori Aktif</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <x-hugeicon name="CheckmarkCircle02Icon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $categories->where('is_active', true)->count() }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Digunakan pada kuesioner</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Kategori Nonaktif</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <x-hugeicon name="UnavailableIcon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-600">{{ $categories->where('is_active', false)->count() }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Disembunyikan sementara</div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold tracking-wider text-slate-500 uppercase">
                            <th class="py-3.5 pl-6 pr-3 w-12 text-center">No.</th>
                            <th class="py-3.5 px-3">Nama Kategori</th>
                            <th class="py-3.5 px-3">Slug</th>
                            <th class="py-3.5 px-3 min-w-[200px]">Deskripsi</th>
                            <th class="py-3.5 px-3 text-center">Ambang Sedang</th>
                            <th class="py-3.5 px-3 text-center">Ambang Tinggi</th>
                            <th class="py-3.5 px-3 text-center">Urutan</th>
                            <th class="py-3.5 px-3 text-center">Status</th>
                            <th class="py-3.5 pl-3 pr-6 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($categories as $index => $category)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 pl-6 pr-3 text-center font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-4 px-3">
                                    <div class="font-bold text-ink">{{ $category->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        {{ $category->questions_count }} pertanyaan &bull; {{ $category->assistance_programs_count }} program
                                    </div>
                                </td>
                                <td class="py-4 px-3 font-mono text-xs text-slate-600">
                                    <span class="rounded bg-slate-100 px-2 py-0.5">{{ $category->slug }}</span>
                                </td>
                                <td class="py-4 px-3 text-xs leading-relaxed text-slate-600">
                                    {{ $category->description ?? '-' }}
                                </td>
                                <td class="py-4 px-3 text-center font-semibold text-amber-700">
                                    {{ number_format((float)$category->moderate_threshold, 0) }}%
                                </td>
                                <td class="py-4 px-3 text-center font-semibold text-rose-700">
                                    {{ number_format((float)$category->high_threshold, 0) }}%
                                </td>
                                <td class="py-4 px-3 text-center font-semibold text-slate-700">
                                    {{ $category->sort_order }}
                                </td>
                                <td class="py-4 px-3 text-center">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                            <span class="size-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 pl-3 pr-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            type="button"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->name }}"
                                            data-category-slug="{{ $category->slug }}"
                                            data-category-description="{{ $category->description ?? '' }}"
                                            data-category-moderate="{{ $category->moderate_threshold }}"
                                            data-category-high="{{ $category->high_threshold }}"
                                            data-category-sort="{{ $category->sort_order }}"
                                            data-category-active="{{ $category->is_active ? '1' : '0' }}"
                                            onclick="openEditModal(this)"
                                            title="Edit Kategori"
                                            class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:border-brand hover:bg-brand-50 hover:text-brand transition-colors"
                                        >
                                            <x-hugeicon name="Edit02Icon" :size="16" />
                                        </button>

                                        <form method="POST" action="{{ route('petugas.kategori-kendala.toggle', $category) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                title="{{ $category->is_active ? 'Nonaktifkan Kategori' : 'Aktifkan Kategori' }}"
                                                class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:border-amber-400 hover:bg-amber-50 hover:text-amber-700 transition-colors"
                                            >
                                                <x-hugeicon name="{{ $category->is_active ? 'ViewOffSlashIcon' : 'ViewIcon' }}" :size="16" />
                                            </button>
                                        </form>

                                        @if($category->questions_count === 0 && $category->assistance_programs_count === 0)
                                            <form
                                                method="POST"
                                                action="{{ route('petugas.kategori-kendala.destroy', $category) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->name }}? Tindakan ini tidak dapat dibatalkan.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Hapus Kategori"
                                                    class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:border-rose-400 hover:bg-rose-50 hover:text-rose-700 transition-colors"
                                                >
                                                    <x-hugeicon name="Delete02Icon" :size="16" />
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                title="Kategori tidak dapat dihapus karena sudah memiliki relasi data pertanyaan atau program bantuan. Nonaktifkan kategori jika tidak digunakan."
                                                class="grid size-8 place-items-center rounded-lg border border-slate-200/50 text-slate-300 cursor-not-allowed"
                                            >
                                                <x-hugeicon name="Delete02Icon" :size="16" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                            <x-hugeicon name="Tag01Icon" :size="24" />
                                        </span>
                                        <p class="font-medium text-slate-600">Belum ada kategori kendala</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Klik tombol Tambah Kategori di atas untuk membuat kategori baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-ink">Tambah Kategori Kendala</h2>
                    <button type="button" onclick="closeCreateModal()" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-hugeicon name="Cancel01Icon" :size="18" />
                    </button>
                </div>

                <form method="POST" action="{{ route('petugas.kategori-kendala.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="create_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="create_name"
                            name="name"
                            required
                            placeholder="Contoh: Digitalisasi Usaha"
                            oninput="autoSlug(this.value, 'create_slug')"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                    </div>

                    <div>
                        <label for="create_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Slug URL <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="create_slug"
                            name="slug"
                            required
                            placeholder="digitalisasi-usaha"
                            class="w-full font-mono rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Harus unik dan hanya huruf kecil, angka, serta tanda strip (-).</p>
                    </div>

                    <div>
                        <label for="create_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi Kategori
                        </label>
                        <textarea
                            id="create_description"
                            name="description"
                            rows="2"
                            placeholder="Penjelasan singkat mengenai bidang kendala ini..."
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_moderate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Ambang Sedang (%) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="create_moderate"
                                name="moderate_threshold"
                                required
                                min="0"
                                max="100"
                                step="0.01"
                                value="40"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>

                        <div>
                            <label for="create_high" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Ambang Tinggi (%) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="create_high"
                                name="high_threshold"
                                required
                                min="0"
                                max="100"
                                step="0.01"
                                value="70"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-1">
                        <div>
                            <label for="create_sort" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="create_sort"
                                name="sort_order"
                                required
                                min="0"
                                value="{{ $categories->count() + 1 }}"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>

                        <div class="sm:pt-5">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    checked
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onclick="closeCreateModal()"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                        >
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-ink">Edit Kategori Kendala</h2>
                    <button type="button" onclick="closeEditModal()" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-hugeicon name="Cancel01Icon" :size="18" />
                    </button>
                </div>

                <form id="edit-form" method="POST" action="" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Kategori <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit_name"
                            name="name"
                            required
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                    </div>

                    <div>
                        <label for="edit_slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Slug URL <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit_slug"
                            name="slug"
                            required
                            class="w-full font-mono rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                        <p class="text-[11px] text-slate-500 mt-1">Harus unik dan hanya huruf kecil, angka, serta tanda strip (-).</p>
                    </div>

                    <div>
                        <label for="edit_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Deskripsi Kategori
                        </label>
                        <textarea
                            id="edit_description"
                            name="description"
                            rows="2"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_moderate" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Ambang Sedang (%) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="edit_moderate"
                                name="moderate_threshold"
                                required
                                min="0"
                                max="100"
                                step="0.01"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>

                        <div>
                            <label for="edit_high" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Ambang Tinggi (%) <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="edit_high"
                                name="high_threshold"
                                required
                                min="0"
                                max="100"
                                step="0.01"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-1">
                        <div>
                            <label for="edit_sort" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="edit_sort"
                                name="sort_order"
                                required
                                min="0"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>

                        <div class="sm:pt-5">
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="edit_is_active"
                                    name="is_active"
                                    value="1"
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onclick="closeEditModal()"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                        >
                            Perbarui Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function autoSlug(text, targetId) {
        const slug = text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w-]+/g, '')
            .replace(/--+/g, '-');
        document.getElementById(targetId).value = slug;
    }

    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-category-id');
        const form = document.getElementById('edit-form');
        form.action = `/petugas/kategori-kendala/${id}`;
        document.getElementById('edit_name').value = button.getAttribute('data-category-name') || '';
        document.getElementById('edit_slug').value = button.getAttribute('data-category-slug') || '';
        document.getElementById('edit_description').value = button.getAttribute('data-category-description') || '';
        document.getElementById('edit_moderate').value = button.getAttribute('data-category-moderate') || 40;
        document.getElementById('edit_high').value = button.getAttribute('data-category-high') || 70;
        document.getElementById('edit_sort').value = button.getAttribute('data-category-sort') || 0;
        document.getElementById('edit_is_active').checked = button.getAttribute('data-category-active') === '1';
        document.getElementById('edit-modal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
</script>
@endpush
