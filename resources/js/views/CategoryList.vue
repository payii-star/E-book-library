<template>
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Kelola Kategori</h1>
        <p class="page-sub">{{ categories.length }} kategori tersedia</p>
      </div>
      <button class="btn-add" @click="showModal = true">＋ Tambah Kategori</button>
    </div>

    <div v-if="loading" class="loading-state">
      <div class="spinner"></div><span>Memuat kategori...</span>
    </div>

    <div v-else class="cat-grid">
      <div v-if="categories.length === 0" class="empty-full">
        <div class="empty-icon">🏷️</div>
        <div>Belum ada kategori. Tambahkan sekarang!</div>
      </div>
      <div class="cat-card" v-for="(cat, i) in categories" :key="cat.id" :style="{'--i': i}">
        <div class="cat-color-bar" :style="{background: catColors[i % catColors.length]}"></div>
        <div class="cat-body">
          <div class="cat-icon-wrap" :style="{background: catColors[i % catColors.length] + '22'}">
            {{ catEmojis[i % catEmojis.length] }}
          </div>
          <div class="cat-info">
            <div class="cat-name">{{ cat.name }}</div>
            <div class="cat-desc">{{ cat.description || 'Tidak ada deskripsi' }}</div>
            <div class="cat-count">{{ cat.books_count ?? 0 }} buku</div>
          </div>
          <button class="cat-del-btn" @click="confirmDelete(cat)">🗑️</button>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header-row">
            <div class="modal-title-text">Tambah Kategori Baru</div>
            <button class="modal-close-btn" @click="closeModal">✕</button>
          </div>
          <div class="field-group">
            <label class="field-label">Nama Kategori *</label>
            <input v-model="newCat.name" class="field-input" placeholder="Contoh: Fiksi, Sejarah, Sains..."/>
            <span v-if="catError" class="field-err">{{ catError }}</span>
          </div>
          <div class="field-group">
            <label class="field-label">Deskripsi</label>
            <textarea v-model="newCat.description" class="field-textarea" rows="3" placeholder="Deskripsi singkat..."></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel-sm" @click="closeModal">Batal</button>
            <button class="btn-save-sm" :disabled="saving" @click="addCategory">
              {{ saving ? 'Menyimpan...' : 'Tambah Kategori' }}
            </button>
          </div>
        </div>
      </div>

      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal-box modal-sm">
          <div class="modal-icon">🗑️</div>
          <div class="modal-title">Hapus Kategori?</div>
          <div class="modal-desc">Kategori <strong>"{{ deleteTarget.name }}"</strong> akan dihapus permanen.</div>
          <div class="modal-actions">
            <button class="btn-cancel-sm" @click="deleteTarget = null">Batal</button>
            <button class="btn-delete" :disabled="deleting" @click="doDelete">{{ deleting ? 'Menghapus...' : 'Ya, Hapus' }}</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-category-list",
  setup() {
    const categories = ref<any[]>([]);
    const loading = ref(true);
    const showModal = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const deleteTarget = ref<any>(null);
    const catError = ref("");
    const newCat = reactive({ name: "", description: "" });

    const catColors = ["#3182ce","#38a169","#d69e2e","#805ad5","#e53e3e","#2b6cb0","#319795","#c05621"];
    const catEmojis = ["📚","🔬","🎭","🌍","🎨","💡","🚀","🌿"];

    const load = async () => {
      loading.value = true;
      try {
        const res = await ApiService.get("categories");
        categories.value = res.data;
      } catch {
        categories.value = [
          { id:1, name:"Fiksi", description:"Novel dan cerita fiksi", books_count: 12 },
          { id:2, name:"Sejarah", description:"Buku sejarah nusantara", books_count: 8 },
          { id:3, name:"Sains", description:"Ilmu pengetahuan alam", books_count: 5 },
        ];
      }
      loading.value = false;
    };

    const closeModal = () => { showModal.value = false; newCat.name = ""; newCat.description = ""; catError.value = ""; };

    const addCategory = async () => {
      if (!newCat.name.trim()) { catError.value = "Nama kategori wajib diisi"; return; }
      saving.value = true;
      try {
        const res = await ApiService.post("admin/categories", { ...newCat });
        categories.value.push({ ...res.data, books_count: 0 });
        closeModal();
      } catch (e: any) {
        catError.value = e.response?.data?.message || "Gagal menambah kategori";
      }
      saving.value = false;
    };

    const confirmDelete = (cat: any) => { deleteTarget.value = cat; };
    const doDelete = async () => {
      if (!deleteTarget.value) return;
      deleting.value = true;
      try {
        await ApiService.delete(`admin/categories/${deleteTarget.value.id}`);
        categories.value = categories.value.filter(c => c.id !== deleteTarget.value.id);
      } catch {}
      deleting.value = false;
      deleteTarget.value = null;
    };

    onMounted(load);
    return { categories, loading, showModal, saving, deleting, deleteTarget, catError, newCat, catColors, catEmojis, closeModal, addCategory, confirmDelete, doDelete };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');
* { font-family: 'Outfit', sans-serif; }
.page-container { display: flex; flex-direction: column; gap: 20px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; }
.page-title { font-size: 22px; font-weight: 800; color: #f0f4ff; margin: 0 0 4px; }
.page-sub { font-size: 13px; color: #4a6080; margin: 0; }
.btn-add { display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg,#2b6cb0,#1C325E); color: white; border: none; border-radius: 12px; padding: 11px 20px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); }
.loading-state { display: flex; align-items: center; gap: 12px; padding: 60px; color: #4a6080; justify-content: center; }
.spinner { width: 24px; height: 24px; border: 2px solid #222; border-top-color: #63b3ed; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.empty-full { grid-column: 1/-1; display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px; color: #4a6080; background: #111; border: 1px solid #222; border-radius: 18px; }
.empty-icon { font-size: 40px; }
.cat-card { background: #111; border: 1px solid #222; border-radius: 18px; overflow: hidden; animation: slideUp 0.35s ease both; animation-delay: calc(var(--i) * 0.05s); transition: all 0.25s; }
.cat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.4); border-color: #333; }
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.cat-color-bar { height: 4px; }
.cat-body { display: flex; align-items: flex-start; gap: 14px; padding: 20px; }
.cat-icon-wrap { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.cat-info { flex: 1; }
.cat-name { font-size: 15px; font-weight: 700; color: #e2e8f0; margin-bottom: 4px; }
.cat-desc { font-size: 12px; color: #4a6080; line-height: 1.4; margin-bottom: 8px; }
.cat-count { font-size: 11px; color: #63b3ed; font-weight: 600; background: rgba(99,179,237,0.1); padding: 2px 8px; border-radius: 100px; display: inline-block; }
.cat-del-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #222; background: #1a1a1a; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; flex-shrink: 0; }
.cat-del-btn:hover { background: rgba(229,62,62,0.15); border-color: rgba(229,62,62,0.3); }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
.modal-box { background: #111; border: 1px solid #333; border-radius: 20px; padding: 28px; width: 100%; max-width: 460px; }
.modal-sm { max-width: 380px; text-align: center; }
.modal-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.modal-title-text { font-size: 17px; font-weight: 700; color: #e2e8f0; }
.modal-close-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #333; background: #1a1a1a; color: #a0aec0; cursor: pointer; font-size: 14px; transition: all 0.2s; }
.modal-icon { font-size: 36px; margin-bottom: 12px; }
.modal-title { font-size: 17px; font-weight: 800; color: #f0f4ff; margin-bottom: 8px; }
.modal-desc { font-size: 13px; color: #718096; line-height: 1.6; margin-bottom: 20px; }
.modal-actions { display: flex; gap: 10px; justify-content: center; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px; }
.field-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.field-label { font-size: 12px; font-weight: 600; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.4px; }
.field-input, .field-textarea { background: #1a1a1a; border: 1.5px solid #333; border-radius: 12px; padding: 11px 14px; color: #e2e8f0; font-size: 14px; outline: none; transition: border-color 0.2s; width: 100%; font-family: 'Outfit', sans-serif; }
.field-input:focus, .field-textarea:focus { border-color: #63b3ed; }
.field-input::placeholder, .field-textarea::placeholder { color: #2d4a6a; }
.field-textarea { resize: vertical; }
.field-err { font-size: 11.5px; color: #fc8181; }
.btn-cancel-sm { padding: 10px 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; font-family: 'Outfit', sans-serif; }
.btn-save-sm { padding: 10px 24px; background: linear-gradient(135deg,#2b6cb0,#1C325E); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; }
.btn-save-sm:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-delete { padding: 10px 24px; background: #e53e3e; border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; }
.btn-delete:disabled { opacity: 0.5; cursor: not-allowed; }
</style>