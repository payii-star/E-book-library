<template>
  <div class="page-container">

    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Kelola Buku</h1>
        <p class="page-sub">{{ totalBooks }} buku tersedia di perpustakaan</p>
      </div>
      <router-link to="/admin/books/create" class="btn-add">
        <span>＋</span> Tambah Buku
      </router-link>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
      <div class="search-wrap">
        <svg class="search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="search" class="search-input" placeholder="Cari judul atau pengarang..." @input="onSearch"/>
      </div>

      <!-- Custom dropdown: Kategori -->
      <div class="custom-select-wrap" ref="catDropRef">
        <button class="custom-select-btn" @click="catOpen = !catOpen">
          <span>{{ filterCategory ? (categories.find(c=>c.id==filterCategory)?.name||'Semua Kategori') : 'Semua Kategori' }}</span>
          <svg class="sel-chevron" :class="{open: catOpen}" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div v-if="catOpen" class="custom-select-dropdown">
          <div class="sel-option" :class="{active: !filterCategory}" @click="filterCategory=''; catOpen=false; loadBooks()">Semua Kategori</div>
          <div class="sel-option" v-for="cat in categories" :key="cat.id" :class="{active: filterCategory==cat.id}" @click="filterCategory=cat.id; catOpen=false; loadBooks()">{{ cat.name }}</div>
        </div>
      </div>

      <!-- Custom dropdown: Status -->
      <div class="custom-select-wrap" ref="statDropRef">
        <button class="custom-select-btn" @click="statOpen = !statOpen">
          <span>{{ filterStatus === 'published' ? 'Published' : filterStatus === 'draft' ? 'Draft' : 'Semua Status' }}</span>
          <svg class="sel-chevron" :class="{open: statOpen}" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <div v-if="statOpen" class="custom-select-dropdown">
          <div class="sel-option" :class="{active: !filterStatus}" @click="filterStatus=''; statOpen=false; loadBooks()">Semua Status</div>
          <div class="sel-option" :class="{active: filterStatus==='published'}" @click="filterStatus='published'; statOpen=false; loadBooks()">
            <span class="sel-dot dot-green"></span> Published
          </div>
          <div class="sel-option" :class="{active: filterStatus==='draft'}" @click="filterStatus='draft'; statOpen=false; loadBooks()">
            <span class="sel-dot dot-yellow"></span> Draft
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div v-if="loading" class="table-loading">
        <div class="spinner"></div>
        <span>Memuat data...</span>
      </div>
      <table v-else class="data-table">
        <thead>
          <tr>
            <th>BUKU</th>
            <th>KATEGORI</th>
            <th>CHAPTER</th>
            <th>VIEWS</th>
            <th>RATING</th>
            <th>STATUS</th>
            <th>AKSI</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="books.length === 0">
            <td colspan="7" class="empty-row">
              <div class="empty-state">
                <div class="empty-icon">📚</div>
                <div>Belum ada buku. <router-link to="/admin/books/create" class="link-blue">Tambah sekarang</router-link></div>
              </div>
            </td>
          </tr>
          <tr v-for="book in books" :key="book.id" class="table-row">
            <td>
              <div class="book-cell">
                <div class="book-thumb" :style="!book.cover_image ? {background: randomGradient(book.id)} : {}">
                  <img v-if="book.cover_image" :src="getCoverUrl(book.cover_image)" class="cover-img" :alt="book.title"/>
                  <span v-else>{{ book.title.charAt(0) }}</span>
                </div>
                <div>
                  <div class="book-cell-title">{{ book.title }}</div>
                  <div class="book-cell-author">{{ book.author }}</div>
                </div>
              </div>
            </td>
            <td><span class="tag-cat">{{ book.category?.name || '—' }}</span></td>
            <td><span class="chapter-count">{{ book.chapters_count ?? '—' }} bab</span></td>
            <td><span class="views-num">{{ (book.total_views || 0).toLocaleString() }}</span></td>
            <td>
              <div class="rating-cell">
                <span class="star-ico">★</span>
                {{ Number(book.reviews_avg_rating || 0).toFixed(1) }}
              </div>
            </td>
            <td>
              <span :class="['status-badge', book.status === 'published' ? 'status-pub' : 'status-draft']">
                {{ book.status === 'published' ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td>
              <div class="action-btns">
                <router-link :to="`/admin/books/${book.id}/edit`" class="act-btn act-edit" title="Edit">✏️</router-link>
                <router-link :to="`/admin/books/${book.id}/chapters`" class="act-btn act-chapter" title="Kelola Chapter">📄</router-link>
                <button class="act-btn act-del" title="Hapus" @click="confirmDelete(book)">🗑️</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button class="page-btn" :disabled="currentPage === 1" @click="goPage(currentPage - 1)">← Prev</button>
        <span class="page-info">{{ currentPage }} / {{ totalPages }}</span>
        <button class="page-btn" :disabled="currentPage === totalPages" @click="goPage(currentPage + 1)">Next →</button>
      </div>
    </div>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal-box">
          <div class="modal-icon">🗑️</div>
          <div class="modal-title">Hapus Buku?</div>
          <div class="modal-desc">Buku <strong>"{{ deleteTarget.title }}"</strong> dan semua chapter-nya akan dihapus permanen.</div>
          <div class="modal-actions">
            <button class="btn-cancel" @click="deleteTarget = null">Batal</button>
            <button class="btn-delete" :disabled="deleting" @click="doDelete">
              {{ deleting ? 'Menghapus...' : 'Ya, Hapus' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, onUnmounted } from "vue";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-book-list",
  setup() {
    const books = ref<any[]>([]);
    const categories = ref<any[]>([]);
    const loading = ref(true);
    const search = ref("");
    const filterCategory = ref<any>("");
    const filterStatus = ref("");
    const currentPage = ref(1);
    const totalPages = ref(1);
    const totalBooks = ref(0);
    const deleteTarget = ref<any>(null);
    const deleting = ref(false);
    const catOpen = ref(false);
    const statOpen = ref(false);
    const catDropRef = ref<HTMLElement|null>(null);
    const statDropRef = ref<HTMLElement|null>(null);
    let searchTimer: any = null;

    // Close on outside click
    const onOutsideClick = (e: MouseEvent) => {
      if (catDropRef.value && !catDropRef.value.contains(e.target as Node)) catOpen.value = false;
      if (statDropRef.value && !statDropRef.value.contains(e.target as Node)) statOpen.value = false;
    };
    onMounted(() => document.addEventListener('click', onOutsideClick));
    onUnmounted(() => document.removeEventListener('click', onOutsideClick));

    const gradients = [
      "linear-gradient(135deg,#3182ce,#2c5282)",
      "linear-gradient(135deg,#38a169,#276749)",
      "linear-gradient(135deg,#d69e2e,#b7791f)",
      "linear-gradient(135deg,#805ad5,#553c9a)",
      "linear-gradient(135deg,#e53e3e,#9b2335)",
      "linear-gradient(135deg,#2b6cb0,#1C325E)",
    ];
    const randomGradient = (id: number) => gradients[id % gradients.length];

    const getCoverUrl = (path: string) => {
      const base = import.meta.env.VITE_APP_API_URL?.replace('/api', '') || 'http://127.0.0.1:8000';
      return `${base}/storage/${path}`;
    };

    const loadBooks = async (page = 1) => {
      loading.value = true;
      try {
        const params: any = { page, per_page: 10 };
        if (search.value) params.search = search.value;
        if (filterCategory.value) params.category_id = filterCategory.value;
        if (filterStatus.value) params.status = filterStatus.value;
        const res = await ApiService.query("admin/books", { params });
        books.value = res.data.data ?? res.data;
        totalPages.value = res.data.last_page ?? 1;
        totalBooks.value = res.data.total ?? books.value.length;
        currentPage.value = page;
      } catch {
        books.value = [];
        totalBooks.value = 0;
      }
      loading.value = false;
    };

    const loadCategories = async () => {
      try {
        const res = await ApiService.get("categories");
        categories.value = res.data;
      } catch {}
    };

    const onSearch = () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => loadBooks(1), 400);
    };

    const goPage = (p: number) => loadBooks(p);
    const confirmDelete = (book: any) => { deleteTarget.value = book; };

    const doDelete = async () => {
      if (!deleteTarget.value) return;
      deleting.value = true;
      try {
        await ApiService.delete(`admin/books/${deleteTarget.value.id}`);
        books.value = books.value.filter(b => b.id !== deleteTarget.value.id);
        totalBooks.value--;
      } catch {}
      deleting.value = false;
      deleteTarget.value = null;
    };

    onMounted(() => { loadBooks(); loadCategories(); });

    return { books, categories, loading, search, filterCategory, filterStatus,
             currentPage, totalPages, totalBooks, deleteTarget, deleting,
             catOpen, statOpen, catDropRef, statDropRef,
             randomGradient, getCoverUrl, loadBooks, onSearch, goPage, confirmDelete, doDelete };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap');
* { font-family: 'Outfit', sans-serif; }

.page-container { display: flex; flex-direction: column; gap: 20px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; }
.page-title { font-size: 22px; font-weight: 800; color: #f0f4ff; margin: 0 0 4px; }
.page-sub { font-size: 13px; color: #4a6080; margin: 0; }

.btn-add { display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #2b6cb0, #1C325E); color: white; border: none; border-radius: 12px; padding: 11px 20px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); color: white; }

.filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #4a6080; }
.search-input { width: 100%; padding: 10px 16px 10px 40px; background: #111; border: 1.5px solid #222; border-radius: 12px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color 0.2s; }
.search-input:focus { border-color: #63b3ed; }
.search-input::placeholder { color: #2d4a6a; }

/* ✅ Custom Select */
.custom-select-wrap { position: relative; min-width: 160px; }
.custom-select-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 10px 14px; background: #111; border: 1.5px solid #222; border-radius: 12px; color: #e2e8f0; font-size: 13px; font-family: 'Outfit', sans-serif; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
.custom-select-btn:hover { border-color: #63b3ed; }
.sel-chevron { color: #4a6080; transition: transform 0.2s; flex-shrink: 0; }
.sel-chevron.open { transform: rotate(180deg); }
.custom-select-dropdown { position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: #141414; border: 1px solid #2a2a2a; border-radius: 12px; overflow: hidden; z-index: 999; box-shadow: 0 12px 32px rgba(0,0,0,0.5); animation: dropIn 0.15s ease; min-width: 160px; }
@keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
.sel-option { display: flex; align-items: center; gap: 8px; padding: 10px 14px; font-size: 13px; color: #a0aec0; cursor: pointer; transition: background 0.15s; }
.sel-option:hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }
.sel-option.active { color: #63b3ed; background: rgba(99,179,237,0.08); }
.sel-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.dot-green { background: #68d391; }
.dot-yellow { background: #f6ad55; }

.table-card { background: #111; border: 1px solid #222; border-radius: 18px; overflow: hidden; }
.table-loading { display: flex; align-items: center; justify-content: center; gap: 12px; padding: 60px; color: #4a6080; }
.spinner { width: 24px; height: 24px; border: 2px solid #222; border-top-color: #63b3ed; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr { background: #0d0d0d; border-bottom: 1px solid #222; }
.data-table th { padding: 14px 16px; font-size: 11px; font-weight: 700; color: #2d4a6a; letter-spacing: 0.8px; text-align: left; }
.table-row { border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.15s; }
.table-row:hover { background: rgba(255,255,255,0.03); }
.table-row td { padding: 14px 16px; font-size: 13px; color: #a0aec0; vertical-align: middle; }
.table-row:last-child { border-bottom: none; }

.empty-row td { padding: 60px; }
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 10px; color: #4a6080; }
.empty-icon { font-size: 40px; }
.link-blue { color: #63b3ed; text-decoration: none; font-weight: 600; }

.book-cell { display: flex; align-items: center; gap: 12px; }
.book-thumb { width: 40px; height: 52px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: white; flex-shrink: 0; overflow: hidden; }
.cover-img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.book-cell-title { font-size: 13px; font-weight: 600; color: #e2e8f0; }
.book-cell-author { font-size: 11px; color: #4a6080; margin-top: 2px; }

.tag-cat { background: rgba(99,179,237,0.1); border: 1px solid rgba(99,179,237,0.2); color: #63b3ed; padding: 3px 10px; border-radius: 100px; font-size: 11px; font-weight: 600; }
.chapter-count { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #718096; }
.views-num { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: #718096; }
.rating-cell { display: flex; align-items: center; gap: 4px; font-family: 'JetBrains Mono', monospace; font-size: 12px; }
.star-ico { color: #f6ad55; }

.status-badge { padding: 4px 12px; border-radius: 100px; font-size: 11px; font-weight: 700; }
.status-pub { background: rgba(104,211,145,0.12); color: #68d391; border: 1px solid rgba(104,211,145,0.2); }
.status-draft { background: rgba(246,173,85,0.12); color: #f6ad55; border: 1px solid rgba(246,173,85,0.2); }

.action-btns { display: flex; gap: 6px; }
.act-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #222; background: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; text-decoration: none; transition: all 0.2s; }
.act-btn:hover { border-color: #333; background: #222; transform: translateY(-1px); }

.pagination { display: flex; align-items: center; justify-content: center; gap: 16px; padding: 16px; border-top: 1px solid #222; }
.page-btn { padding: 8px 16px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.page-btn:hover:not(:disabled) { background: #222; color: #e2e8f0; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.page-info { font-size: 13px; color: #4a6080; font-family: 'JetBrains Mono', monospace; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal-box { background: #111; border: 1px solid #333; border-radius: 20px; padding: 36px; text-align: center; max-width: 380px; width: 90%; }
.modal-icon { font-size: 40px; margin-bottom: 12px; }
.modal-title { font-size: 18px; font-weight: 800; color: #f0f4ff; margin-bottom: 8px; }
.modal-desc { font-size: 13px; color: #718096; line-height: 1.6; margin-bottom: 24px; }
.modal-actions { display: flex; gap: 10px; justify-content: center; }
.btn-cancel { padding: 10px 24px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.btn-cancel:hover { background: #222; }
.btn-delete { padding: 10px 24px; background: #e53e3e; border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-delete:hover:not(:disabled) { background: #c53030; }
.btn-delete:disabled { opacity: 0.5; cursor: not-allowed; }
</style>