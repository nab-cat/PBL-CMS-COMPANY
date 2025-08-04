<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Dashboard Testimoni</h2>
                    <p class="text-sm text-gray-600">Kelola semua jenis testimoni dari satu tempat</p>
                </div>
            </div>
        </div>        
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Testimoni Produk</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\TestimoniProduk::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\TestimoniProduk::where('status', 'terpublikasi')->count() }} terpublikasi</p>
                    </div>
                    <div class="p-2 bg-green-100 rounded-lg">
                        <x-heroicon-o-shopping-bag class="w-6 h-6 text-green-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Testimoni Lowongan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\TestimoniLowongan::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\TestimoniLowongan::where('status', 'terpublikasi')->count() }} terpublikasi</p>
                    </div>
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <x-heroicon-o-briefcase class="w-6 h-6 text-blue-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Testimoni Event</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\TestimoniEvent::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\TestimoniEvent::where('status', 'terpublikasi')->count() }} terpublikasi</p>
                    </div>
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <x-heroicon-o-calendar-days class="w-6 h-6 text-purple-600" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Testimoni Artikel</p>
                        <p class="text-2xl font-bold text-gray-900">{{ \App\Models\TestimoniArtikel::count() }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ \App\Models\TestimoniArtikel::where('status', 'terpublikasi')->count() }} terpublikasi</p>
                    </div>
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <x-heroicon-o-document-text class="w-6 h-6 text-orange-600" />
                    </div>
                </div>
            </div>
        </div>
   
        
        <!-- Recent Testimonials -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Testimoni Terbaru</h3>
            <div class="space-y-3">
                @php
                    $recentTestimonials = collect();
                    
                    // Ambil testimoni terbaru dari setiap jenis
                    $produkTestimonials = \App\Models\TestimoniProduk::with(['user', 'produk'])
                        ->latest()
                        ->take(2)
                        ->get()
                        ->map(function($item) {
                            return [
                                'type' => 'Produk',
                                'nama' => $item->user?->name ?? 'Anonim',
                                'isi_testimoni' => $item->isi_testimoni,
                                'related' => $item->produk?->nama_produk ?? 'Produk tidak ditemukan',
                                'rating' => $item->rating,
                                'created_at' => $item->created_at,
                                'status' => $item->status,
                            ];
                        });
                    
                    $lowonganTestimonials = \App\Models\TestimoniLowongan::with(['user', 'lowongan'])
                        ->latest()
                        ->take(2)
                        ->get()
                        ->map(function($item) {
                            return [
                                'type' => 'Lowongan',
                                'nama' => $item->user?->name ?? 'Anonim',
                                'isi_testimoni' => $item->isi_testimoni,
                                'related' => $item->lowongan?->judul_lowongan ?? 'Lowongan tidak ditemukan',
                                'rating' => $item->rating,
                                'created_at' => $item->created_at,
                                'status' => $item->status,
                            ];
                        });
                    
                    $eventTestimonials = \App\Models\TestimoniEvent::with(['user', 'event'])
                        ->latest()
                        ->take(2)
                        ->get()
                        ->map(function($item) {
                            return [
                                'type' => 'Event',
                                'nama' => $item->user?->name ?? 'Anonim',
                                'isi_testimoni' => $item->isi_testimoni,
                                'related' => $item->event?->nama_event ?? 'Event tidak ditemukan',
                                'rating' => $item->rating,
                                'created_at' => $item->created_at,
                                'status' => $item->status,
                            ];
                        });
                    
                    $artikelTestimonials = \App\Models\TestimoniArtikel::with(['user', 'artikel'])
                        ->latest()
                        ->take(2)
                        ->get()
                        ->map(function($item) {
                            return [
                                'type' => 'Artikel',
                                'nama' => $item->user?->name ?? 'Anonim',
                                'isi_testimoni' => $item->isi_testimoni,
                                'related' => $item->artikel?->judul_artikel ?? 'Artikel tidak ditemukan',
                                'rating' => $item->rating,
                                'created_at' => $item->created_at,
                                'status' => $item->status,
                            ];
                        });
                    
                    $recentTestimonials = $recentTestimonials
                        ->concat($produkTestimonials)
                        ->concat($lowonganTestimonials)
                        ->concat($eventTestimonials)
                        ->concat($artikelTestimonials)
                        ->sortByDesc('created_at')
                        ->take(8);
                @endphp

                @forelse($recentTestimonials as $testimonial)
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="p-1 bg-{{ $testimonial['type'] == 'Produk' ? 'green' : ($testimonial['type'] == 'Lowongan' ? 'blue' : ($testimonial['type'] == 'Event' ? 'purple' : 'orange')) }}-100 rounded">
                            <span class="text-xs font-medium text-{{ $testimonial['type'] == 'Produk' ? 'green' : ($testimonial['type'] == 'Lowongan' ? 'blue' : ($testimonial['type'] == 'Event' ? 'purple' : 'orange')) }}-700">{{ $testimonial['type'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-medium text-gray-900">{{ $testimonial['nama'] }}</p>
                                <div class="flex items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $testimonial['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($testimonial['isi_testimoni'] ?? 'Tidak ada konten', 100) }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $testimonial['related'] }} • {{ $testimonial['created_at']->diffForHumans() }} • Status: {{ $testimonial['status'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                        <p>Belum ada testimoni</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
