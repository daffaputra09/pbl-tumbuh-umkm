@extends('layouts.app-shell')

@section('title', 'Pengelompokan — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Pengelompokan</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">
                    Usaha dikelompokkan dari kendala utama pada kuesioner yang sedang berlaku.
                </p>
            </div>
            <form method="get" action="{{ request()->url() }}" class="flex items-center gap-2">
                <label for="kategori" class="sr-only">Kategori kendala</label>
                <select id="kategori" name="kategori" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink sm:w-64">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategoryId === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex shrink-0 items-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-ink hover:bg-slate-50">Filter</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Usaha</th>
                            <th class="px-4 py-3 font-semibold">Pemilik</th>
                            <th class="px-4 py-3 font-semibold">Kendala utama</th>
                            <th class="px-4 py-3 font-semibold">Tingkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($rows as $row)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $row['businessName'] }}</td>
                                <td class="px-4 py-3">{{ $row['ownerName'] }}</td>
                                <td class="px-4 py-3">{{ $row['category'] }}</td>
                                <td class="px-4 py-3">{{ $row['level'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada usaha yang menyelesaikan kuesioner kebutuhan dan kendala.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
