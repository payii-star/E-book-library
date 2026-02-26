<template>
  <div class="page-wrap">
    <div class="page-header">
      <h1 class="page-title">📚 Perpustakaan</h1>
      <p class="page-sub">{{ total }} buku tersedia untuk kamu baca</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
      <div class="search-wrap">
        <svg class="s-ico" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="search" class="s-input" placeholder="Cari judul, pengarang..." @input="onSearch"/>
      </div>
      <select v-model="catFilter" class="f-select" @change="load(1)">
        <option value="">Semua Kategori</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <select v-model="sortBy" class="f-select" @change="load(1)">
        <option value="latest">Terbaru</option>
        <option value="popular">Terpopuler</option>
        <option value="rating">Rating Tertinggi</option>
        <option value="title">A-Z</option>
      </select>
    </div>

    <!-- Books Grid -->
    <div v-if="loading" class="books-grid">
      <div class="skel-card" v-for="i in 12" :key="i"></div>
    </div>

    <div v-else-if="books.length === 0" class="empty-state">
      <div class="empty-ico">🔍</div>
      <div class="empty-txt">Tidak ada buku ditemukan</div>
      <button class="empty-reset" @click="resetFilter">Reset Filter</button>
    </div>

    <div v-else class="books-grid">
      <router-link v-for="(b,i) in books" :key="b.id" :to="`/user/books/${b.slug}`" class="book-card" :style="{'--i':i}">
        <!-- Cover: tampil foto kalau ada, fallback ke gradient -->
        <div class="bc-cover" :style="!b.cover_image ? {background: grad(b.id)} : {}">
          <img v-if="b.cover_image" :src="getCoverUrl(b.cover_image)" class="bc-img" :alt="b.title"/>
          <template v-else>
            <span class="bc-letter">{{ b.title.charAt(0) }}</span>
            <div class="bc-shine"></div>
          </template>
          <div class="bc-chapters">{{ b.chapters_count ?? 0 }} Bab</div>
        </div>
        <div class="bc-body">
          <div class="bc-cat">{{ b.category?.name }}</div>
          <div class="bc-title">{{ b.title }}</div>
          <div class="bc-author">{{ b.author }}</div>
          <div class="bc-bottom">
            <span class="bc-star">★ {{ Number(b.reviews_avg_rating||0).toFixed(1) }}</span>
            <span class="bc-dot">·</span>
            <span class="bc-views">{{ b.total_views }} dibaca</span>
          </div>
        </div>
      </router-link>
    </div>

    <!-- Pagination -->
    <div v-if="lastPage > 1" class="pagination">
      <button class="pg-btn" :disabled="page===1" @click="load(page-1)">← Prev</button>
      <span class="pg-info">{{ page }} / {{ lastPage }}</span>
      <button class="pg-btn" :disabled="page===lastPage" @click="load(page+1)">Next →</button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "user-book-list",
  setup() {
    const route = useRoute();
    const books = ref<any[]>([]);
    const categories = ref<any[]>([]);
    const loading = ref(true);
    const search = ref("");
    const catFilter = ref("");
    const sortBy = ref("latest");
    const page = ref(1);
    const lastPage = ref(1);
    const total = ref(0);
    let timer: any = null;

    const grads = ["linear-gradient(145deg,#1a365d,#2b6cb0)","linear-gradient(145deg,#1a4731,#276749)","linear-gradient(145deg,#5b3a00,#d69e2e)","linear-gradient(145deg,#3b1f6e,#805ad5)","linear-gradient(145deg,#631a1a,#e53e3e)","linear-gradient(145deg,#0d4a4a,#319795)","linear-gradient(145deg,#2d1b69,#553c9a)","linear-gradient(145deg,#4a1942,#b83280)"];
    const grad = (id: number) => grads[id % grads.length];
    const getCoverUrl = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace('/api', '') || 'http://127.0.0.1:8000';
      return `${base}/storage/${path}`;
    };

    const load = async (p = 1) => {
      loading.value = true;
      try {
        const params: any = { page: p, per_page: 12, sort: sortBy.value };
        if (search.value) params.search = search.value;
        if (catFilter.value) params.category_id = catFilter.value;
        const r = await ApiService.query("books", { params });
        books.value = r.data.data ?? r.data;
        lastPage.value = r.data.last_page ?? 1;
        total.value = r.data.total ?? books.value.length;
        page.value = p;
      } catch { books.value = []; }
      loading.value = false;
    };

    const onSearch = () => { clearTimeout(timer); timer = setTimeout(() => load(1), 400); };
    const resetFilter = () => { search.value = ""; catFilter.value = ""; sortBy.value = "latest"; load(1); };

    onMounted(async () => {
      if (route.query.sort) sortBy.value = route.query.sort as string;
      try { const r = await ApiService.get("categories"); categories.value = r.data; } catch {}
      load(1);
    });

    watch(() => route.query.sort, (v) => { if (v) { sortBy.value = v as string; load(1); } });

    return { books, categories, loading, search, catFilter, sortBy, page, lastPage, total, grad, getCoverUrl, load, onSearch, resetFilter };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap');
* { font-family: 'DM Sans', sans-serif; }
.page-wrap { display: flex; flex-direction: column; gap: 24px; }
.page-title { font-family: 'Fraunces', serif; font-size: 24px; font-weight: 900; color: #f0f4ff; margin: 0 0 4px; }
.page-sub { font-size: 13px; color: #4a6080; margin: 0; }
.filter-bar { display: flex; gap: 10px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.s-ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #4a6080; }
.s-input { width: 100%; padding: 10px 14px 10px 38px; background: #0d0d0d; border: 1.5px solid #1a1a1a; border-radius: 12px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color 0.2s; font-family: 'DM Sans', sans-serif; }
.s-input:focus { border-color: #2b6cb0; }
.s-input::placeholder { color: #2d4a6a; }
.f-select { padding: 10px 14px; background: #0d0d0d; border: 1.5px solid #1a1a1a; border-radius: 12px; color: #e2e8f0; font-size: 13px; outline: none; cursor: pointer; font-family: 'DM Sans', sans-serif; min-width: 140px; }
.f-select:focus { border-color: #2b6cb0; }
.books-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 20px; }
.skel-card { height: 280px; background: linear-gradient(90deg,#0d0d0d,#1a1a1a,#0d0d0d); background-size: 200%; border-radius: 14px; animation: shimmer 1.5s infinite; }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
.book-card { text-decoration: none; transition: transform 0.25s; animation: fadeUp 0.4s ease both; animation-delay: calc(var(--i) * 0.04s); }
.book-card:hover { transform: translateY(-6px); }
@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
.bc-cover { height: 200px; border-radius: 14px 14px 0 0; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.bc-img { width: 100%; height: 100%; object-fit: cover; }
.bc-letter { font-family: 'Fraunces', serif; font-size: 56px; font-weight: 900; color: rgba(255,255,255,0.85); z-index: 1; }
.bc-shine { position: absolute; top: 0; left: 0; width: 45%; height: 100%; background: linear-gradient(90deg,rgba(255,255,255,0.1),transparent); }
.bc-chapters { position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.55); color: rgba(255,255,255,0.9); font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 100px; backdrop-filter: blur(4px); z-index: 2; }
.bc-body { background: #0d0d0d; border: 1px solid #1a1a1a; border-top: none; border-radius: 0 0 14px 14px; padding: 12px 14px; }
.bc-cat { font-size: 10px; color: #63b3ed; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
.bc-title { font-size: 13px; font-weight: 700; color: #e2e8f0; line-height: 1.3; margin-bottom: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.bc-author { font-size: 11px; color: #4a6080; margin-bottom: 8px; }
.bc-bottom { display: flex; align-items: center; gap: 5px; }
.bc-star { font-size: 11px; color: #f6ad55; font-weight: 700; }
.bc-dot { color: #2d4a6a; font-size: 10px; }
.bc-views { font-size: 11px; color: #4a6080; }
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 80px 20px; color: #4a6080; }
.empty-ico { font-size: 48px; }
.empty-txt { font-size: 15px; font-weight: 600; }
.empty-reset { padding: 10px 24px; background: #0d0d0d; border: 1px solid #333; border-radius: 10px; color: #63b3ed; font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif; }
.pagination { display: flex; align-items: center; justify-content: center; gap: 16px; padding: 8px; }
.pg-btn { padding: 10px 20px; background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.pg-btn:hover:not(:disabled) { background: #1a1a1a; color: #e2e8f0; }
.pg-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.pg-info { font-size: 13px; color: #4a6080; }
</style>