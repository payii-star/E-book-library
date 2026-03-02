<template>
  <div class="user-dashboard">
    <!-- Hero Banner -->
    <div class="hero-banner">
      <div class="hero-bg"><div class="hero-grid"></div><div class="hero-glow"></div></div>
      <div class="hero-content">
        <div class="hero-left">
          <div class="hero-tag">E-Library</div>
          <h1 class="hero-name">Halo, {{ authStore.user?.name?.split(' ')[0] }} 👋</h1>
          <p class="hero-sub">Temukan buku favoritmu dan mulai membaca sekarang</p>
          <router-link to="/user/books" class="hero-cta">Jelajahi Perpustakaan →</router-link>
        </div>
      </div>
    </div>

    <!-- Lanjutkan Membaca -->
    <div class="section" v-if="histories.length > 0">
      <div class="sec-header">
        <div class="sec-title">📖 Lanjutkan Membaca</div>
      </div>
      <div class="history-list">
        <router-link
          v-for="h in histories"
          :key="h.id"
          :to="`/user/books/${h.book?.slug}/read/${h.chapter?.chapter_number}`"
          class="history-card"
        >
          <!-- Cover -->
          <div class="hc-cover" :style="!h.book?.cover_image ? {background: grad(h.book_id)} : {}">
            <img v-if="h.book?.cover_image" :src="getCoverUrl(h.book.cover_image)" class="hc-img" :alt="h.book?.title" />
            <span v-else class="hc-letter">{{ h.book?.title?.charAt(0) }}</span>
          </div>
          <!-- Info -->
          <div class="hc-info">
            <div class="hc-title">{{ h.book?.title }}</div>
            <div class="hc-author">{{ h.book?.author }}</div>
            <div class="hc-bab">
              <span class="hc-bab-badge">Bab {{ h.chapter?.chapter_number }}</span>
              <span class="hc-bab-name">{{ h.chapter?.title }}</span>
            </div>
          </div>
          <!-- Arrow -->
          <div class="hc-arrow">→</div>
        </router-link>
      </div>
    </div>

    <!-- Terpopuler -->
    <div class="section">
      <div class="sec-header">
        <div class="sec-title">🔥 Terpopuler</div>
        <router-link to="/user/books?sort=popular" class="sec-link">Lihat semua →</router-link>
      </div>
      <div class="books-scroll">
        <div v-if="loadingPop" class="book-skel" v-for="i in 5" :key="i"></div>
        <template v-else>
          <div v-if="popular.length===0" class="empty-hint">📚 Belum ada buku</div>
          <router-link v-for="(b,i) in popular" :key="i" :to="`/user/books/${b.slug}`" class="book-card">
            <div class="bc-cover" :style="!b.cover_image ? {background:grad(i)} : {}">
              <img v-if="b.cover_image" :src="getCoverUrl(b.cover_image)" class="cover-img" :alt="b.title" />
              <template v-else>
                <span class="bc-letter">{{ b.title.charAt(0) }}</span>
                <div class="bc-shine"></div>
              </template>
            </div>
            <div class="bc-info">
              <div class="bc-title">{{ b.title }}</div>
              <div class="bc-author">{{ b.author }}</div>
              <div class="bc-meta">
                <span class="bc-star">★ {{ Number(b.reviews_avg_rating||0).toFixed(1) }}</span>
                <span class="bc-view">{{ b.total_views }} dibaca</span>
              </div>
            </div>
          </router-link>
        </template>
      </div>
    </div>

    <!-- Terbaru -->
    <div class="section">
      <div class="sec-header">
        <div class="sec-title">✨ Terbaru</div>
        <router-link to="/user/books?sort=latest" class="sec-link">Lihat semua →</router-link>
      </div>
      <div class="books-scroll">
        <div v-if="loadingNew" class="book-skel" v-for="i in 4" :key="i"></div>
        <template v-else>
          <router-link v-for="(b,i) in newest" :key="i" :to="`/user/books/${b.slug}`" class="book-card">
            <div class="bc-cover" :style="!b.cover_image ? {background:grad(i+5)} : {}">
              <img v-if="b.cover_image" :src="getCoverUrl(b.cover_image)" class="cover-img" :alt="b.title" />
              <template v-else>
                <span class="bc-letter">{{ b.title.charAt(0) }}</span>
                <div class="bc-shine"></div>
                <div class="bc-new">BARU</div>
              </template>
              <div v-if="b.cover_image" class="bc-new">BARU</div>
            </div>
            <div class="bc-info">
              <div class="bc-title">{{ b.title }}</div>
              <div class="bc-author">{{ b.author }}</div>
              <div class="bc-meta"><span class="bc-cat">{{ b.category?.name }}</span></div>
            </div>
          </router-link>
        </template>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "user-dashboard",
  setup() {
    const authStore = useAuthStore();
    const popular  = ref<any[]>([]);
    const newest   = ref<any[]>([]);
    const histories = ref<any[]>([]);
    const loadingPop = ref(true);
    const loadingNew = ref(true);

    const grads = ["linear-gradient(145deg,#1a365d,#2b6cb0)","linear-gradient(145deg,#1a4731,#276749)","linear-gradient(145deg,#5b3a00,#d69e2e)","linear-gradient(145deg,#3b1f6e,#805ad5)","linear-gradient(145deg,#631a1a,#e53e3e)","linear-gradient(145deg,#0d4a4a,#319795)","linear-gradient(145deg,#2d1b69,#553c9a)","linear-gradient(145deg,#4a1942,#b83280)"];
    const grad = (i: number) => grads[i % grads.length];

    const getCoverUrl = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace('/api', '') || 'http://127.0.0.1:8000';
      return `${base}/storage/${path}`;
    };

    onMounted(async () => {
      // Load riwayat baca
      try {
        ApiService.setHeader();
        const h = await ApiService.get("reading-history", "");
        histories.value = h.data ?? [];
      } catch {}

      // Load popular
      try {
        const r = await ApiService.query("books", { params: { sort: "popular", per_page: 8 } });
        popular.value = r.data.data ?? r.data;
      } catch {}
      loadingPop.value = false;

      // Load newest
      try {
        const r = await ApiService.query("books", { params: { sort: "latest", per_page: 6 } });
        newest.value = r.data.data ?? r.data;
      } catch {}
      loadingNew.value = false;
    });

    return { authStore, popular, newest, histories, loadingPop, loadingNew, grad, getCoverUrl };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap');
* { font-family: 'DM Sans', sans-serif; }
.user-dashboard { display: flex; flex-direction: column; gap: 32px; }

/* Hero */
.hero-banner { border-radius: 20px; overflow: hidden; position: relative; }
.hero-bg { position: absolute; inset: 0; background: linear-gradient(135deg,#060e1f,#0d1f3c); }
.hero-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(99,179,237,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,179,237,0.04) 1px,transparent 1px); background-size: 32px 32px; }
.hero-glow { position: absolute; top: -80px; right: -40px; width: 360px; height: 360px; background: radial-gradient(circle,rgba(43,108,176,0.2),transparent 65%); }
.hero-content { position: relative; z-index: 1; display: flex; align-items: center; padding: 40px 44px; gap: 24px; }
.hero-tag { font-size: 11px; font-weight: 700; color: #63b3ed; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 10px; }
.hero-name { font-family: 'Fraunces', serif; font-size: 34px; font-weight: 900; color: #f0f4ff; margin: 0 0 8px; line-height: 1.1; }
.hero-sub { font-size: 14px; color: #4a6080; margin: 0 0 22px; }
.hero-cta { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg,#2b6cb0,#1a365d); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 700; transition: all 0.2s; }
.hero-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(43,108,176,0.5); color: white; }

/* Section */
.section { display: flex; flex-direction: column; gap: 14px; }
.sec-header { display: flex; align-items: center; justify-content: space-between; }
.sec-title { font-size: 16px; font-weight: 700; color: #e2e8f0; font-family: 'Fraunces', serif; }
.sec-link { font-size: 13px; color: #63b3ed; text-decoration: none; font-weight: 600; }

/* Riwayat Baca */
.history-list { display: flex; flex-direction: column; gap: 10px; }
.history-card { display: flex; align-items: center; gap: 14px; padding: 14px 16px; background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 14px; text-decoration: none; transition: all 0.2s; }
.history-card:hover { border-color: #2b6cb0; background: #0a1628; }
.hc-cover { width: 48px; height: 64px; border-radius: 8px; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.hc-img { width: 100%; height: 100%; object-fit: cover; }
.hc-letter { font-family: 'Fraunces', serif; font-size: 22px; font-weight: 900; color: rgba(255,255,255,0.8); }
.hc-info { flex: 1; min-width: 0; }
.hc-title { font-size: 14px; font-weight: 700; color: #e2e8f0; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hc-author { font-size: 12px; color: #4a6080; margin-bottom: 6px; }
.hc-bab { display: flex; align-items: center; gap: 8px; }
.hc-bab-badge { font-size: 10px; font-weight: 800; color: white; background: linear-gradient(135deg,#2b6cb0,#1a365d); padding: 2px 8px; border-radius: 100px; white-space: nowrap; }
.hc-bab-name { font-size: 12px; color: #718096; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hc-arrow { font-size: 16px; color: #2d4a6a; flex-shrink: 0; transition: all 0.2s; }
.history-card:hover .hc-arrow { color: #63b3ed; transform: translateX(4px); }

/* Buku scroll */
.books-scroll { display: flex; gap: 14px; overflow-x: auto; padding-bottom: 6px; scrollbar-width: thin; scrollbar-color: #1a1a1a transparent; }
.books-scroll::-webkit-scrollbar { height: 3px; }
.books-scroll::-webkit-scrollbar-thumb { background: #1a1a1a; border-radius: 100px; }
.book-skel { flex-shrink: 0; width: 140px; height: 250px; background: linear-gradient(90deg,#0d0d0d,#1a1a1a,#0d0d0d); background-size: 200%; border-radius: 12px; animation: shimmer 1.5s infinite; }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
.book-card { flex-shrink: 0; width: 140px; text-decoration: none; transition: transform 0.25s; }
.book-card:hover { transform: translateY(-6px); }
.bc-cover { height: 190px; border-radius: 12px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
.cover-img { width: 100%; height: 100%; object-fit: cover; }
.bc-letter { font-family: 'Fraunces', serif; font-size: 48px; font-weight: 900; color: rgba(255,255,255,0.85); z-index: 1; }
.bc-shine { position: absolute; top: 0; left: 0; width: 45%; height: 100%; background: linear-gradient(90deg,rgba(255,255,255,0.1),transparent); }
.bc-new { position: absolute; top: 8px; right: 8px; background: #63b3ed; color: white; font-size: 9px; font-weight: 800; padding: 2px 7px; border-radius: 100px; z-index: 2; }
.bc-info { padding: 8px 2px 0; }
.bc-title { font-size: 12px; font-weight: 700; color: #e2e8f0; margin-bottom: 2px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.bc-author { font-size: 11px; color: #4a6080; margin-bottom: 5px; }
.bc-meta { display: flex; gap: 6px; flex-wrap: wrap; }
.bc-star { font-size: 11px; color: #f6ad55; font-weight: 700; }
.bc-view { font-size: 11px; color: #4a6080; }
.bc-cat { font-size: 10px; color: #63b3ed; background: rgba(99,179,237,0.1); padding: 2px 7px; border-radius: 100px; }
.empty-hint { color: #4a6080; font-size: 13px; padding: 20px; }

@media(max-width:640px) { .hero-content{padding:24px;} .hero-name{font-size:24px;} }
</style>