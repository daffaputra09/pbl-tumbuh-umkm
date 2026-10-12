@extends('layouts.app-shell')

@section('title', 'Edit Program Bantuan — TUMBUH UMKM')

@section('content')
    <div class="space-y-6 max-w-5xl mx-auto">
        <div>
            <a href="{{ route('petugas.program-bantuan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand transition-colors mb-2">
                <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                Kembali ke Daftar Program
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Edit Program Bantuan</h1>
            <p class="mt-1 text-sm text-slate-600">
                Perbarui detail program atau sesuaikan kategori kendala yang terhubung dengan program ini.
            </p>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <div class="font-semibold mb-1">Terdapat kesalahan pengisian formulir:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('petugas.program-bantuan.update', $program) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Bagian 1: Data Utama Program --}}
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-ink">Informasi Program Bantuan</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Identitas, penyelenggara, kuota, dan masa berlaku program.</p>
                    </div>
                    <span class="text-xs text-slate-400">ID Program #{{ $program->id }}</span>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Program <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $program->name) }}"
                            class="w-full rounded-xl border @error('name') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                            required
                        >
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="provider" class="block text-xs font-semibold text-slate-700 mb-1">
                            Penyelenggara <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="provider"
                            name="provider"
                            value="{{ old('provider', $program->provider) }}"
                            class="w-full rounded-xl border @error('provider') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                            required
                        >
                        @error('provider')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                            Status Program <span class="text-rose-500">*</span>
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-xl border @error('status') border-rose-400 @else border-slate-200 @enderror bg-white px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                            required
                        >
                            <option value="draft" @selected(old('status', $program->status) === 'draft')>Draf (Belum Siap Direkomendasikan)</option>
                            <option value="active" @selected(old('status', $program->status) === 'active')>Aktif (Siap Direkomendasikan)</option>
                            <option value="closed" @selected(old('status', $program->status) === 'closed')>Ditutup</option>
                            <option value="archived" @selected(old('status', $program->status) === 'archived')>Diarsipkan</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                            Deskripsi Program <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full rounded-xl border @error('description') border-rose-400 bg-rose-50/30 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                            required
                        >{{ old('description', $program->description) }}</textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="requirements" class="block text-xs font-semibold text-slate-700 mb-1">
                            Persyaratan Penerima <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea
                            id="requirements"
                            name="requirements"
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                        >{{ old('requirements', $program->requirements) }}</textarea>
                        @error('requirements')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="url" class="block text-xs font-semibold text-slate-700 mb-1">
                            Tautan Informasi / Pendaftaran <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="url"
                            id="url"
                            name="url"
                            value="{{ old('url', $program->url) }}"
                            class="w-full rounded-xl border @error('url') border-rose-400 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                        >
                        @error('url')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quota" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kuota Penerima <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="number"
                            id="quota"
                            name="quota"
                            value="{{ old('quota', $program->quota) }}"
                            min="1"
                            class="w-full rounded-xl border @error('quota') border-rose-400 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                        >
                        @error('quota')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="starts_on" class="block text-xs font-semibold text-slate-700 mb-1">
                            Periode Mulai <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="date"
                            id="starts_on"
                            name="starts_on"
                            value="{{ old('starts_on', $program->starts_on?->format('Y-m-d')) }}"
                            class="w-full rounded-xl border @error('starts_on') border-rose-400 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                        >
                        @error('starts_on')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ends_on" class="block text-xs font-semibold text-slate-700 mb-1">
                            Periode Selesai <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="date"
                            id="ends_on"
                            name="ends_on"
                            value="{{ old('ends_on', $program->ends_on?->format('Y-m-d')) }}"
                            class="w-full rounded-xl border @error('ends_on') border-rose-400 @else border-slate-200 @enderror px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                        >
                        @error('ends_on')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Integrasi Kategori Kendala (Target Utama UC-08) --}}
            <div class="rounded-2xl border @error('obstacle_category_ids') border-rose-300 ring-2 ring-rose-100 @else border-slate-200/80 @enderror bg-white p-6 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-ink">Integrasi Kategori Kendala</h2>
                            <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[11px] font-bold text-brand">Wajib Minimal 1</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kategori yang terhubung sebelumnya ditampilkan di bawah ini. Anda dapat menambah, mengubah, atau membatalkan pilihan.
                        </p>
                    </div>
                    <span class="text-xs font-medium text-slate-500">
                        {{ $categories->count() }} kategori tersedia
                    </span>
                </div>

                @error('obstacle_category_ids')
                    <div class="flex items-center gap-2 rounded-xl bg-rose-50 border border-rose-200 px-3.5 py-2.5 text-xs font-medium text-rose-800">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-rose-600 shrink-0">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 pt-1">
                    @php
                        $attachedCategories = $program->obstacleCategories->keyBy('id');
                        $selectedCategoryIds = old('obstacle_category_ids') !== null
                            ? collect(old('obstacle_category_ids', []))->map(fn($v) => (int)$v)->all()
                            : $attachedCategories->keys()->all();
                        $oldLevels = old('minimum_levels');
                    @endphp

                    @forelse($categories as $category)
                        @php
                            $isChecked = in_array($category->id, $selectedCategoryIds, true);
                            $attachedPivot = $attachedCategories->get($category->id);
                            $currentLevel = $oldLevels !== null
                                ? ($oldLevels[$category->id] ?? 'moderate')
                                : ($attachedPivot ? $attachedPivot->pivot->minimum_level : 'moderate');
                        @endphp
                        <div
                            id="card-cat-{{ $category->id }}"
                            class="category-card rounded-xl border p-4 transition-all {{ $isChecked ? 'border-brand bg-brand-50/30 shadow-xs' : 'border-slate-200 bg-white hover:border-slate-300' }}"
                        >
                            <div class="flex items-start gap-3">
                                <div class="pt-0.5">
                                    <input
                                        type="checkbox"
                                        id="cat-checkbox-{{ $category->id }}"
                                        name="obstacle_category_ids[]"
                                        value="{{ $category->id }}"
                                        @checked($isChecked)
                                        onchange="toggleCategoryCard('{{ $category->id }}', this.checked)"
                                        class="size-4.5 rounded border-slate-300 text-brand focus:ring-brand cursor-pointer"
                                    >
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <label for="cat-checkbox-{{ $category->id }}" class="block font-semibold text-sm text-ink cursor-pointer">
                                            {{ $category->name }}
                                        </label>
                                        @if($attachedPivot)
                                            <span class="rounded bg-teal-100/70 px-1.5 py-0.2 text-[10px] font-semibold text-teal-800">
                                                Saat ini terhubung
                                            </span>
                                        @endif
                                    </div>
                                    @if($category->description)
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                            {{ $category->description }}
                                        </p>
                                    @endif

                                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <span class="text-[11px] font-medium text-slate-500">Tingkat Kendala Minimum:</span>
                                        <select
                                            name="minimum_levels[{{ $category->id }}]"
                                            id="level-cat-{{ $category->id }}"
                                            class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs text-ink focus:border-brand focus:outline-none"
                                            {{ $isChecked ? '' : 'disabled' }}
                                        >
                                            <option value="moderate" @selected($currentLevel === 'moderate')>Sedang (>= {{ (int)$category->moderate_threshold }}%)</option>
                                            <option value="high" @selected($currentLevel === 'high')>Tinggi (>= {{ (int)$category->high_threshold }}%)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 rounded-xl border border-dashed border-slate-200 p-6 text-center text-slate-500 text-sm">
                            Tidak ada kategori kendala yang dapat dihubungkan.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a
                    href="{{ route('petugas.program-bantuan.index') }}"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                    Perbarui Program Bantuan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function toggleCategoryCard(id, checked) {
                const card = document.getElementById('card-cat-' + id);
                const levelSelect = document.getElementById('level-cat-' + id);

                if (checked) {
                    card.classList.add('border-brand', 'bg-brand-50/30', 'shadow-xs');
                    card.classList.remove('border-slate-200', 'bg-white');
                    if (levelSelect) levelSelect.removeAttribute('disabled');
                } else {
                    card.classList.remove('border-brand', 'bg-brand-50/30', 'shadow-xs');
                    card.classList.add('border-slate-200', 'bg-white');
                    if (levelSelect) levelSelect.setAttribute('disabled', 'disabled');
                }
            }
        </script>
    @endpush
@endsection
