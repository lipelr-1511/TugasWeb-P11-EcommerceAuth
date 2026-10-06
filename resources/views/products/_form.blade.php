@csrf

<div class="space-y-4">
    <div>
        <x-input-label for="category_id" value="Kategori" />
        <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            <option value="">-- Pilih kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="title" value="Nama Produk" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $product->title)" />
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="description" value="Deskripsi" />
        <textarea id="description" name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $product->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="price" value="Harga (Rp)" />
            <x-text-input id="price" name="price" type="number" step="0.01" class="mt-1 block w-full" :value="old('price', $product->price)" />
            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="stock" value="Stok" />
            <x-text-input id="stock" name="stock" type="number" class="mt-1 block w-full" :value="old('stock', $product->stock ?? 0)" />
            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label value="Tag" />
        @php($selectedTags = old('tags', $product->tags->pluck('id')->all()))
        <div class="mt-1 flex flex-wrap gap-4">
            @foreach ($tags as $tag)
                <label class="inline-flex items-center gap-1 text-sm">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
                    {{ $tag->name }}
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>Simpan</x-primary-button>
        <a href="{{ route('products.index') }}" class="text-sm text-gray-600 underline">Batal</a>
    </div>
</div>
