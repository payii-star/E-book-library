<template>
  <div class="page-container">

    <div class="page-header">
      <div>
        <h1 class="page-title">Laporan Lengkap</h1>
        <p class="page-sub">Statistik detail perpustakaan</p>
      </div>
    </div>

    <div v-if="loading" class="text-center py-10">
      <div class="spinner-border text-primary"></div>
    </div>

    <template v-else>
      <!-- Stat Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,0.15);color:#60a5fa">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 19.5A2.5 2.5 0 016.5 17H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" stroke="currentColor" stroke-width="1.7"/></svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_books ?? 0 }}</div>
            <div class="stat-label">Total Buku</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,0.15);color:#34d399">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.7"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_users ?? 0 }}</div>
            <div class="stat-label">Total Anggota</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(245,158,11,0.15);color:#fbbf24">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/></svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_reads ?? 0 }}</div>
            <div class="stat-label">Total Dibaca</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(139,92,246,0.15);color:#a78bfa">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.total_reviews ?? 0 }}</div>
            <div class="stat-label">Total Review</div>
          </div>
        </div>
      </div>

      <!-- Two col -->
      <div class="two-col">

        <!-- Buku Terpopuler -->
        <div class="report-card">
          <div class="card-head">
            <h3 class="card-title">Buku Terpopuler</h3>
            <span class="card-badge">Top 5</span>
          </div>
          <div v-if="stats.most_read_books?.length" class="book-list">
            <div v-for="(book, i) in stats.most_read_books" :key="book.id" class="book-row">
              <div class="book-rank">#{{ i + 1 }}</div>
              <img v-if="book.cover_image" :src="getCover(book.cover_image)" class="book-thumb" />
              <div v-else class="book-thumb-placeholder">📚</div>
              <div class="book-meta">
                <div class="book-name">{{ book.title }}</div>
                <div class="book-author">{{ book.author }}</div>
              </div>
              <div class="book-reads">{{ book.total_views ?? 0 }} <span>dibaca</span></div>
            </div>
          </div>
          <div v-else class="empty-state">Belum ada data</div>
        </div>

        <!-- Rating Tertinggi -->
        <div class="report-card">
          <div class="card-head">
            <h3 class="card-title">Rating Tertinggi</h3>
            <span class="card-badge">Top 5</span>
          </div>
          <div v-if="stats.top_rated_books?.length" class="book-list">
            <div v-for="(book, i) in stats.top_rated_books" :key="book.id" class="book-row">
              <div class="book-rank">#{{ i + 1 }}</div>
              <img v-if="book.cover_image" :src="getCover(book.cover_image)" class="book-thumb" />
              <div v-else class="book-thumb-placeholder">📚</div>
              <div class="book-meta">
                <div class="book-name">{{ book.title }}</div>
                <div class="stars">
                  <span v-for="s in 5" :key="s" :style="{ color: s <= Math.round(book.reviews_avg_rating ?? 0) ? '#fbbf24' : '#374151' }">★</span>
                  <span class="rating-num">{{ Number(book.reviews_avg_rating ?? 0).toFixed(1) }}</span>
                </div>
              </div>
              <div class="book-reads">{{ book.reviews_count ?? 0 }} <span>review</span></div>
            </div>
          </div>
          <div v-else class="empty-state">Belum ada data</div>
        </div>

      </div>

      <!-- Aktivitas Terbaru -->
      <div class="report-card mt-5">
        <div class="card-head">
          <h3 class="card-title">Aktivitas Terbaru</h3>
          <span class="card-badge">10 terakhir</span>
        </div>
        <div v-if="stats.recent_reads?.length" class="activity-list">
          <div v-for="(act, i) in stats.recent_reads" :key="i" class="activity-row">
            <div class="act-dot" :style="{ background: dotColor(i) }"></div>
            <div class="act-text">
              <span class="act-user">{{ act.user?.name }}</span>
              membaca
              <span class="act-book">{{ act.book?.title }}</span>
            </div>
            <div class="act-time">{{ timeAgo(act.read_at) }}</div>
          </div>
        </div>
        <div v-else class="empty-state">Belum ada aktivitas</div>
      </div>

    </template>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-reports",
  setup() {
    const loading = ref(true);
    const stats = ref<any>({});

    const getCover = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace("/api", "") ?? "http://127.0.0.1:8000";
      return path.startsWith("http") ? path : `${base}/storage/${path}`;
    };

    const dotColors = ["#60a5fa", "#34d399", "#fbbf24", "#a78bfa", "#f87171"];
    const dotColor = (i: number) => dotColors[i % dotColors.length];

    const timeAgo = (dateStr: string) => {
      if (!dateStr) return "";
      const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
      if (diff < 60) return `${diff} dtk lalu`;
      if (diff < 3600) return `${Math.floor(diff / 60)} mnt lalu`;
      if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
      return `${Math.floor(diff / 86400)} hari lalu`;
    };

    onMounted(async () => {
      try {
        ApiService.setHeader();
        const { data } = await ApiService.get("admin/statistics");
        stats.value = data;
      } catch (e) {
        console.error(e);
      } finally {
        loading.value = false;
      }
    });

    return { loading, stats, getCover, dotColor, timeAgo };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@600;700&family=DM+Sans:wght@400;500&display=swap');

.page-container { padding: 24px; font-family: 'DM Sans', sans-serif; }
.page-header { margin-bottom: 28px; }
.page-title { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: #f1f5f9; margin: 0; }
.page-sub { font-size: 13px; color: #64748b; margin: 4px 0 0; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
.stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 20px; display: flex; align-items: center; gap: 14px; }
.stat-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.stat-value { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 700; color: #f1f5f9; }
.stat-label { font-size: 12px; color: #64748b; margin-top: 2px; }

.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

.report-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px; }
.mt-5 { margin-top: 20px; }
.card-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 600; color: #e2e8f0; margin: 0; }
.card-badge { font-size: 11px; font-weight: 500; color: #60a5fa; background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.2); border-radius: 20px; padding: 2px 10px; }

.book-list { display: flex; flex-direction: column; gap: 12px; }
.book-row { display: flex; align-items: center; gap: 12px; }
.book-rank { font-family: 'Syne', sans-serif; font-size: 12px; font-weight: 700; color: #475569; width: 24px; text-align: center; }
.book-thumb { width: 38px; height: 50px; border-radius: 6px; object-fit: cover; flex-shrink: 0; }
.book-thumb-placeholder { width: 38px; height: 50px; border-radius: 6px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.book-meta { flex: 1; min-width: 0; }
.book-name { font-size: 13px; font-weight: 500; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.book-author { font-size: 12px; color: #64748b; margin-top: 2px; }
.book-reads { font-size: 12px; color: #64748b; text-align: right; white-space: nowrap; }
.book-reads span { display: block; font-size: 11px; }
.stars { display: flex; align-items: center; gap: 2px; font-size: 13px; margin-top: 3px; }
.rating-num { font-size: 12px; color: #94a3b8; margin-left: 4px; }

.activity-list { display: flex; flex-direction: column; gap: 12px; }
.activity-row { display: flex; align-items: center; gap: 12px; }
.act-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.act-text { flex: 1; font-size: 13px; color: #94a3b8; }
.act-user { color: #60a5fa; font-weight: 500; }
.act-book { color: #e2e8f0; font-weight: 500; }
.act-time { font-size: 12px; color: #475569; white-space: nowrap; }

.empty-state { text-align: center; color: #475569; font-size: 13px; padding: 20px 0; }

@media (max-width: 768px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
  .two-col { grid-template-columns: 1fr; }
}
</style>