<template>
  <div class="admin-dashboard">

    <!-- Top Stats Bar -->
    <div class="stats-grid">
      <div class="stat-card" v-for="(stat, i) in stats" :key="i" :style="{'--i': i}">
        <div class="stat-icon" :style="{background: stat.bg}">
          <span v-html="stat.icon"></span>
        </div>
        <div class="stat-body">
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-label">{{ stat.label }}</div>
        </div>
        <div class="stat-trend" :class="stat.up ? 'up' : 'down'">
          {{ stat.up ? '↑' : '↓' }} {{ stat.change }}
        </div>
      </div>
    </div>

    <!-- Buku Terpopuler (full width) -->
    <div class="panel">
      <div class="panel-header">
        <div>
          <div class="panel-title">Buku Terpopuler</div>
          <div class="panel-sub">Berdasarkan total pembaca</div>
        </div>
        <router-link to="/admin/books" class="panel-action">Kelola Buku →</router-link>
      </div>
      <div class="book-list">
        <div class="book-row" v-for="(book, i) in mostReadBooks" :key="i">
          <div class="book-rank">{{ String(i+1).padStart(2,'0') }}</div>
          <div class="book-cover-thumb" :style="!book.cover_image ? {background: bookColors[i % bookColors.length]} : {}">
            <img v-if="book.cover_image" :src="getCoverUrl(book.cover_image)" class="thumb-img" />
            <span v-else>{{ book.title.charAt(0) }}</span>
          </div>
          <div class="book-meta">
            <div class="book-title-text">{{ book.title }}</div>
            <div class="book-author-text">{{ book.author }}</div>
          </div>
          <div class="book-bar-wrap">
            <div class="book-bar" :style="{width: (book.total_views / maxViews * 100) + '%', background: bookColors[i % bookColors.length]}"></div>
          </div>
          <div class="book-views">{{ book.total_views.toLocaleString() }}</div>
        </div>
        <div v-if="!mostReadBooks.length" class="empty-state">Belum ada data buku</div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <div class="qa-title">Aksi Cepat</div>
      <div class="qa-grid">
        <router-link to="/admin/books/create" class="qa-card qa-blue">
          <div class="qa-icon">📖</div>
          <div class="qa-label">Tambah Buku</div>
          <div class="qa-desc">Upload buku baru ke perpustakaan</div>
        </router-link>
        <router-link to="/admin/categories" class="qa-card qa-teal">
          <div class="qa-icon">🏷️</div>
          <div class="qa-label">Kelola Kategori</div>
          <div class="qa-desc">Atur genre & kategori buku</div>
        </router-link>
        <router-link to="/admin/users" class="qa-card qa-indigo">
          <div class="qa-icon">👥</div>
          <div class="qa-label">Data Anggota</div>
          <div class="qa-desc">Lihat semua member aktif</div>
        </router-link>
        <router-link to="/admin/reports" class="qa-card qa-purple">
          <div class="qa-icon">📊</div>
          <div class="qa-label">Laporan Lengkap</div>
          <div class="qa-desc">Statistik detail perpustakaan</div>
        </router-link>
      </div>
    </div>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-dashboard",
  setup() {
    const mostReadBooks = ref<any[]>([]);
    const stats = ref([
      { label: "Total Buku",    value: "0", change: "0 buku baru",   up: true, bg: "rgba(99,179,237,0.15)",  icon: "📚" },
      { label: "Total Anggota", value: "0", change: "0 baru",        up: true, bg: "rgba(104,211,145,0.15)", icon: "👥" },
      { label: "Total Dibaca",  value: "0", change: "hari ini",      up: true, bg: "rgba(246,173,85,0.15)",  icon: "👁️" },
      { label: "Total Review",  value: "0", change: "komentar baru", up: true, bg: "rgba(183,148,244,0.15)", icon: "⭐" },
    ]);

    const bookColors = [
      "linear-gradient(135deg,#3182ce,#2c5282)",
      "linear-gradient(135deg,#38a169,#276749)",
      "linear-gradient(135deg,#d69e2e,#b7791f)",
      "linear-gradient(135deg,#805ad5,#553c9a)",
      "linear-gradient(135deg,#e53e3e,#9b2335)",
    ];

    const getCoverUrl = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace('/api', '') || 'http://127.0.0.1:8000';
      return `${base}/storage/${path}`;
    };

    const maxViews = computed(() =>
      mostReadBooks.value.reduce((max, b) => Math.max(max, b.total_views), 1)
    );

    const loadStats = async () => {
      try {
        ApiService.setHeader();
        const res = await ApiService.get("admin/statistics");
        const d = res.data;
        stats.value[0].value = d.total_books?.toLocaleString() ?? "0";
        stats.value[1].value = d.total_users?.toLocaleString() ?? "0";
        stats.value[2].value = d.total_reads?.toLocaleString() ?? "0";
        stats.value[3].value = d.total_reviews?.toLocaleString() ?? "0";
        mostReadBooks.value  = d.most_read_books ?? [];
      } catch {}
    };

    onMounted(loadStats);

    return { stats, mostReadBooks, bookColors, maxViews, getCoverUrl };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap');

.admin-dashboard { font-family: 'Outfit', sans-serif; padding: 8px 0; display: flex; flex-direction: column; gap: 24px; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card { background: #111; border: 1px solid #222; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; position: relative; overflow: hidden; animation: slideUp 0.4s ease both; animation-delay: calc(var(--i) * 0.07s); transition: all 0.25s; }
.stat-card:hover { border-color: #333; transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.4); }
.stat-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.02) 0%, transparent 60%); pointer-events: none; }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
.stat-body { flex: 1; }
.stat-value { font-size: 24px; font-weight: 800; color: #f0f4ff; line-height: 1.1; font-family: 'JetBrains Mono', monospace; }
.stat-label { font-size: 12px; color: #4a6080; font-weight: 500; margin-top: 3px; }
.stat-trend { font-size: 11px; font-weight: 600; }
.stat-trend.up { color: #68d391; }
.stat-trend.down { color: #fc8181; }

.panel { background: #111; border: 1px solid #222; border-radius: 18px; padding: 24px; animation: slideUp 0.4s ease 0.15s both; }
.panel-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
.panel-title { font-size: 16px; font-weight: 700; color: #e2e8f0; }
.panel-sub { font-size: 12px; color: #4a6080; margin-top: 2px; }
.panel-action { font-size: 12px; color: #63b3ed; font-weight: 600; text-decoration: none; white-space: nowrap; transition: color 0.2s; }
.panel-action:hover { color: #90cdf4; }

.book-list { display: flex; flex-direction: column; gap: 12px; }
.book-row { display: flex; align-items: center; gap: 14px; padding: 10px 12px; border-radius: 12px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04); transition: all 0.2s; }
.book-row:hover { background: rgba(255,255,255,0.05); border-color: #333; }
.book-rank { font-size: 11px; font-weight: 700; color: #2d4a6a; font-family: 'JetBrains Mono', monospace; width: 22px; flex-shrink: 0; }
.book-cover-thumb { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; color: white; flex-shrink: 0; overflow: hidden; }
.thumb-img { width: 100%; height: 100%; object-fit: cover; }
.book-meta { flex-shrink: 0; width: 200px; }
.book-title-text { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.book-author-text { font-size: 11px; color: #4a6080; margin-top: 1px; }
.book-bar-wrap { flex: 1; height: 6px; background: #1a1a1a; border-radius: 100px; overflow: hidden; }
.book-bar { height: 100%; border-radius: 100px; transition: width 1s cubic-bezier(0.4,0,0.2,1); }
.book-views { font-size: 12px; font-weight: 600; color: #718096; font-family: 'JetBrains Mono', monospace; width: 50px; text-align: right; flex-shrink: 0; }
.empty-state { text-align: center; color: #2d4a6a; font-size: 13px; padding: 20px 0; }

.quick-actions { animation: slideUp 0.4s ease 0.25s both; }
.qa-title { font-size: 16px; font-weight: 700; color: #e2e8f0; margin-bottom: 14px; }
.qa-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.qa-card { border-radius: 16px; padding: 20px; text-decoration: none; display: flex; flex-direction: column; gap: 6px; border: 1px solid rgba(255,255,255,0.06); transition: all 0.25s; position: relative; overflow: hidden; }
.qa-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(255,255,255,0.06) 0%, transparent 60%); pointer-events: none; }
.qa-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,0.4); }
.qa-blue   { background: rgba(49,130,206,0.12); border-color: rgba(99,179,237,0.2); }
.qa-teal   { background: rgba(56,178,172,0.12); border-color: rgba(79,209,197,0.2); }
.qa-indigo { background: rgba(102,126,234,0.12); border-color: rgba(127,156,245,0.2); }
.qa-purple { background: rgba(128,90,213,0.12); border-color: rgba(183,148,244,0.2); }
.qa-icon { font-size: 26px; margin-bottom: 4px; }
.qa-label { font-size: 14px; font-weight: 700; color: #e2e8f0; }
.qa-desc { font-size: 11.5px; color: #4a6080; line-height: 1.4; }

@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 1200px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .qa-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr 1fr; } .qa-grid { grid-template-columns: 1fr 1fr; } }
</style>