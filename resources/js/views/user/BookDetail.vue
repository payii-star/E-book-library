<template>
  <div class="page-wrap" v-if="book">
    <!-- Header -->
    <div class="book-hero">
      <!-- ✅ Cover image support -->
      <div class="bh-cover" :style="!book.cover_image ? {background: grad(book.id)} : {}">
        <img v-if="book.cover_image" :src="getCoverUrl(book.cover_image)" class="cover-img" :alt="book.title" />
        <template v-else>
          <span class="bh-letter">{{ book.title.charAt(0) }}</span>
          <div class="bh-shine"></div>
        </template>
      </div>
      <div class="bh-info">
        <div class="bh-cat">{{ book.category?.name }}</div>
        <h1 class="bh-title">{{ book.title }}</h1>
        <div class="bh-author">✍️ {{ book.author }}</div>
        <div v-if="book.publisher" class="bh-pub">🏢 {{ book.publisher }} {{ book.published_year ? '· ' + book.published_year : '' }}</div>
        <div class="bh-stats">
          <div class="bhs"><span class="bhs-ico">★</span><span class="bhs-val">{{ Number(book.reviews_avg_rating||0).toFixed(1) }}</span><span class="bhs-lbl">Rating</span></div>
          <div class="bhs"><span class="bhs-ico">👁</span><span class="bhs-val">{{ book.total_views }}</span><span class="bhs-lbl">Dibaca</span></div>
          <div class="bhs"><span class="bhs-ico">📄</span><span class="bhs-val">{{ book.chapters?.length ?? 0 }}</span><span class="bhs-lbl">Bab</span></div>
          <div class="bhs"><span class="bhs-ico">💬</span><span class="bhs-val">{{ book.reviews_count ?? 0 }}</span><span class="bhs-lbl">Review</span></div>
        </div>
        <div class="bh-actions">
          <router-link v-if="book.chapters?.length > 0" :to="`/user/books/${book.slug}/read/1`" class="btn-read">📖 Mulai Baca</router-link>
          <router-link v-if="bookmark" :to="`/user/books/${book.slug}/read/${bookmark.chapter?.chapter_number}`" class="btn-continue">🔖 Lanjutkan Bab {{ bookmark.chapter?.chapter_number }}</router-link>
          <button class="btn-bookmark" :class="{bookmarked: !!bookmark}" @click="toggleBookmark">
            {{ bookmark ? '🔖 Tersimpan' : '+ Simpan' }}
          </button>
        </div>
      </div>
    </div>

    <div class="content-grid">
      <!-- Left -->
      <div class="content-left">
        <div class="card" v-if="book.synopsis">
          <div class="card-title">Sinopsis</div>
          <p class="synopsis-text">{{ book.synopsis }}</p>
        </div>
        <div class="card">
          <div class="card-title">Daftar Bab ({{ book.chapters?.length ?? 0 }})</div>
          <div v-if="!book.chapters || book.chapters.length === 0" class="chap-empty">Belum ada bab tersedia</div>
          <div class="chap-list">
            <router-link v-for="ch in book.chapters" :key="ch.id" :to="`/user/books/${book.slug}/read/${ch.chapter_number}`" class="chap-item">
              <div class="chap-num">{{ ch.chapter_number }}</div>
              <div class="chap-info">
                <div class="chap-title">{{ ch.title }}</div>
                <div class="chap-reads">{{ ch.read_count }} pembaca</div>
              </div>
              <div class="chap-arrow">→</div>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Right: Reviews -->
      <div class="content-right">
        <div class="card">
          <div class="card-title">Tulis Review</div>
          <div class="star-picker">
            <span v-for="s in 5" :key="s" class="star-pick" :class="{active: myRating >= s}" @click="myRating = s">★</span>
          </div>
          <textarea v-model="myComment" class="review-input" rows="3" placeholder="Bagikan pendapatmu tentang buku ini..."></textarea>
          <button class="btn-submit-review" :disabled="!myRating || savingReview" @click="submitReview">
            {{ savingReview ? 'Menyimpan...' : 'Kirim Review' }}
          </button>
        </div>
        <div class="card">
          <div class="card-title">Review ({{ reviews.length }})</div>
          <div v-if="reviews.length === 0" class="empty-reviews">Belum ada review. Jadilah yang pertama!</div>
          <div class="reviews-list">
            <div class="review-item" v-for="rv in reviews" :key="rv.id">
              <div class="rv-header">
                <div class="rv-avatar" :style="{background: grad(rv.user_id)}">{{ rv.user?.name?.charAt(0) }}</div>
                <div class="rv-meta">
                  <div class="rv-name">{{ rv.user?.name }}</div>
                  <div class="rv-stars">{{ '★'.repeat(rv.rating) }}{{ '☆'.repeat(5-rv.rating) }}</div>
                </div>
              </div>
              <div class="rv-text">{{ rv.comment }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else-if="loading" class="loading-page">
    <div class="spinner"></div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "user-book-detail",
  setup() {
    const route = useRoute();
    const book = ref<any>(null);
    const reviews = ref<any[]>([]);
    const bookmark = ref<any>(null);
    const loading = ref(true);
    const myRating = ref(0);
    const myComment = ref("");
    const savingReview = ref(false);

    const grads = ["linear-gradient(145deg,#1a365d,#2b6cb0)","linear-gradient(145deg,#1a4731,#276749)","linear-gradient(145deg,#5b3a00,#d69e2e)","linear-gradient(145deg,#3b1f6e,#805ad5)","linear-gradient(145deg,#631a1a,#e53e3e)","linear-gradient(145deg,#0d4a4a,#319795)","linear-gradient(145deg,#2d1b69,#553c9a)","linear-gradient(145deg,#4a1942,#b83280)"];
    const grad = (id: number) => grads[id % grads.length];

    // ✅ Helper URL cover
    const getCoverUrl = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace('/api', '') || 'http://127.0.0.1:8000';
      return `${base}/storage/${path}`;
    };

    const loadBook = async () => {
      loading.value = true;
      try {
        const r = await ApiService.get(`books/${route.params.slug}`);
        book.value = r.data;
      } catch {}
      loading.value = false;
    };

    const loadReviews = async () => {
      try {
        const r = await ApiService.get(`books/${book.value?.id}/reviews`);
        reviews.value = r.data.data ?? r.data;
      } catch {}
    };

    const loadBookmark = async () => {
      try {
        const r = await ApiService.get("bookmarks");
        bookmark.value = r.data.find((b: any) => b.book_id === book.value?.id) ?? null;
      } catch {}
    };

    const toggleBookmark = async () => {
      if (bookmark.value) {
        try { await ApiService.delete(`bookmarks/${book.value.id}`); bookmark.value = null; } catch {}
      } else {
        if (!book.value?.chapters?.length) return;
        try {
          const r = await ApiService.post("bookmarks", { book_id: book.value.id, chapter_id: book.value.chapters[0].id });
          bookmark.value = r.data;
        } catch {}
      }
    };

    const submitReview = async () => {
      if (!myRating.value) return;
      savingReview.value = true;
      try {
        await ApiService.post(`books/${book.value.id}/reviews`, { rating: myRating.value, comment: myComment.value });
        await loadReviews();
        myRating.value = 0;
        myComment.value = "";
      } catch {}
      savingReview.value = false;
    };

    onMounted(async () => {
      await loadBook();
      if (book.value) { loadReviews(); loadBookmark(); }
    });

    return { book, reviews, bookmark, loading, myRating, myComment, savingReview, grad, getCoverUrl, toggleBookmark, submitReview };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap');
* { font-family: 'DM Sans', sans-serif; }
.page-wrap { display: flex; flex-direction: column; gap: 24px; }
.loading-page { display: flex; justify-content: center; padding: 80px; }
.spinner { width: 32px; height: 32px; border: 3px solid #1a1a1a; border-top-color: #63b3ed; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to{transform:rotate(360deg)} }

.book-hero { display: flex; gap: 28px; align-items: flex-start; flex-wrap: wrap; }
.bh-cover { width: 160px; height: 220px; border-radius: 14px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; flex-shrink: 0; box-shadow: 0 16px 40px rgba(0,0,0,0.5); }
/* ✅ Cover image */
.cover-img { width: 100%; height: 100%; object-fit: cover; }
.bh-letter { font-family: 'Fraunces', serif; font-size: 64px; font-weight: 900; color: rgba(255,255,255,0.85); z-index: 1; }
.bh-shine { position: absolute; top: 0; left: 0; width: 45%; height: 100%; background: linear-gradient(90deg,rgba(255,255,255,0.12),transparent); }
.bh-info { flex: 1; min-width: 260px; }
.bh-cat { font-size: 11px; color: #63b3ed; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
.bh-title { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 900; color: #f0f4ff; margin: 0 0 10px; line-height: 1.2; }
.bh-author { font-size: 14px; color: #a0aec0; margin-bottom: 4px; }
.bh-pub { font-size: 13px; color: #4a6080; margin-bottom: 16px; }
.bh-stats { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px; }
.bhs { display: flex; flex-direction: column; align-items: center; gap: 2px; }
.bhs-ico { font-size: 16px; }
.bhs-val { font-size: 15px; font-weight: 700; color: #e2e8f0; }
.bhs-lbl { font-size: 10px; color: #4a6080; }
.bh-actions { display: flex; gap: 10px; flex-wrap: wrap; }
.btn-read { padding: 11px 22px; background: linear-gradient(135deg,#2b6cb0,#1a365d); color: white; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 700; transition: all 0.2s; }
.btn-read:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); color: white; }
.btn-continue { padding: 11px 22px; background: rgba(99,179,237,0.1); border: 1px solid rgba(99,179,237,0.2); color: #63b3ed; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 700; transition: all 0.2s; }
.btn-bookmark { padding: 11px 22px; background: #0d0d0d; border: 1px solid #1a1a1a; color: #a0aec0; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.btn-bookmark.bookmarked { background: rgba(246,173,85,0.1); border-color: rgba(246,173,85,0.2); color: #f6ad55; }

.content-grid { display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start; }
.content-left { display: flex; flex-direction: column; gap: 16px; }
.content-right { display: flex; flex-direction: column; gap: 16px; }
.card { background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 16px; padding: 22px; }
.card-title { font-size: 15px; font-weight: 700; color: #e2e8f0; margin-bottom: 16px; font-family: 'Fraunces', serif; }
.synopsis-text { font-size: 14px; color: #718096; line-height: 1.8; margin: 0; }

.chap-empty { color: #4a6080; font-size: 13px; text-align: center; padding: 20px; }
.chap-list { display: flex; flex-direction: column; gap: 6px; }
.chap-item { display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: #111; border: 1px solid #1a1a1a; border-radius: 12px; text-decoration: none; transition: all 0.2s; }
.chap-item:hover { border-color: #2b6cb0; background: #0a1628; }
.chap-num { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg,#2b6cb0,#1a365d); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: white; flex-shrink: 0; }
.chap-info { flex: 1; }
.chap-title { font-size: 13px; font-weight: 600; color: #e2e8f0; }
.chap-reads { font-size: 11px; color: #4a6080; margin-top: 1px; }
.chap-arrow { font-size: 14px; color: #2d4a6a; transition: all 0.2s; }
.chap-item:hover .chap-arrow { color: #63b3ed; transform: translateX(3px); }

.star-picker { display: flex; gap: 4px; margin-bottom: 10px; }
.star-pick { font-size: 24px; color: #2d4a6a; cursor: pointer; transition: color 0.15s; }
.star-pick.active { color: #f6ad55; }
.review-input { width: 100%; background: #111; border: 1.5px solid #1a1a1a; border-radius: 12px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; resize: vertical; outline: none; transition: border-color 0.2s; font-family: 'DM Sans', sans-serif; margin-bottom: 10px; }
.review-input:focus { border-color: #2b6cb0; }
.review-input::placeholder { color: #2d4a6a; }
.btn-submit-review { width: 100%; padding: 11px; background: linear-gradient(135deg,#2b6cb0,#1a365d); border: none; border-radius: 12px; color: white; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif; }
.btn-submit-review:disabled { opacity: 0.5; cursor: not-allowed; }
.empty-reviews { color: #4a6080; font-size: 13px; text-align: center; padding: 16px 0; }
.reviews-list { display: flex; flex-direction: column; gap: 12px; }
.review-item { padding: 14px; background: #111; border: 1px solid #1a1a1a; border-radius: 12px; }
.rv-header { display: flex; gap: 10px; align-items: center; margin-bottom: 8px; }
.rv-avatar { width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: white; flex-shrink: 0; }
.rv-name { font-size: 13px; font-weight: 700; color: #e2e8f0; }
.rv-stars { font-size: 12px; color: #f6ad55; letter-spacing: 1px; margin-top: 1px; }
.rv-text { font-size: 13px; color: #718096; line-height: 1.6; }

@media(max-width:900px) { .content-grid{grid-template-columns:1fr;} .bh-cover{width:120px;height:160px;} .bh-title{font-size:22px;} }
</style>