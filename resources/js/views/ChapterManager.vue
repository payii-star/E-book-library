<template>
  <div class="page-container">

    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Kelola Chapter</h1>
        <p class="page-sub" v-if="bookTitle">📖 {{ bookTitle }}</p>
      </div>
      <div class="header-actions">
        <router-link :to="`/admin/books/${bookId}/edit`" class="btn-back">← Edit Buku</router-link>
        <button class="btn-add" @click="openAdd">＋ Tambah Chapter</button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div><span>Memuat chapter...</span>
    </div>

    <!-- Empty -->
    <div v-else-if="chapters.length === 0" class="empty-state">
      <div class="empty-icon">📄</div>
      <div class="empty-txt">Belum ada chapter. Tambahkan sekarang!</div>
      <button class="btn-add" @click="openAdd">＋ Tambah Chapter Pertama</button>
    </div>

    <!-- Chapter List -->
    <div v-else class="chapter-list">
      <div class="chapter-card" v-for="(ch, i) in chapters" :key="ch.id" :style="{'--i': i}">
        <div class="cc-left">
          <div class="cc-num">#{{ ch.chapter_number }}</div>
          <div class="cc-info">
            <div class="cc-title">{{ ch.title }}</div>
            <div class="cc-meta">{{ ch.read_count ?? 0 }} pembaca · {{ ch.content?.length ?? 0 }} karakter</div>
          </div>
        </div>
        <div class="cc-actions">
          <button class="act-btn act-edit" @click="openEdit(ch)">✏️ Edit</button>
          <button class="act-btn act-del" @click="confirmDelete(ch)">🗑️ Hapus</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-box">
          <div class="modal-header-row">
            <div class="modal-title-txt">{{ editingChapter ? 'Edit Chapter' : 'Tambah Chapter Baru' }}</div>
            <button class="modal-close" @click="closeModal">✕</button>
          </div>

          <div class="field-group">
            <label class="field-label">Nomor Bab *</label>
            <input v-model="form.chapter_number" class="field-input" type="number" placeholder="1, 2, 3..."/>
            <span v-if="formErrors.chapter_number" class="field-err">{{ formErrors.chapter_number }}</span>
          </div>

          <div class="field-group">
            <label class="field-label">Judul Bab *</label>
            <input v-model="form.title" class="field-input" placeholder="Judul chapter ini..."/>
            <span v-if="formErrors.title" class="field-err">{{ formErrors.title }}</span>
          </div>

          <div class="field-group">
            <label class="field-label">Isi Konten *</label>
            <div class="content-toolbar">
              <span class="char-count">{{ form.content.length }} karakter</span>
            </div>
            <textarea
              v-model="form.content"
              class="field-textarea content-area"
              rows="14"
              placeholder="Tulis isi bab di sini...&#10;&#10;Tekan Enter untuk paragraf baru."
            ></textarea>
            <span v-if="formErrors.content" class="field-err">{{ formErrors.content }}</span>
          </div>

          <div class="modal-footer">
            <button class="btn-cancel" @click="closeModal">Batal</button>
            <button class="btn-save" :disabled="saving" @click="saveChapter">
              <span v-if="saving"><div class="spin-sm"></div> Menyimpan...</span>
              <span v-else>{{ editingChapter ? '💾 Simpan Perubahan' : '➕ Tambah Chapter' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Delete Confirm -->
      <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget = null">
        <div class="modal-box modal-sm">
          <div class="modal-icon">🗑️</div>
          <div class="modal-title">Hapus Chapter?</div>
          <div class="modal-desc">Chapter <strong>"{{ deleteTarget.title }}"</strong> akan dihapus permanen beserta semua komentarnya.</div>
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
import { defineComponent, ref, reactive, onMounted } from "vue";
import { useRoute } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-chapter-manager",
  setup() {
    const route = useRoute();
    const bookId = route.params.id;

    const chapters  = ref<any[]>([]);
    const bookTitle = ref("");
    const loading   = ref(true);
    const saving    = ref(false);
    const deleting  = ref(false);
    const showModal = ref(false);
    const editingChapter = ref<any>(null);
    const deleteTarget   = ref<any>(null);

    const form = reactive({ chapter_number: "", title: "", content: "" });
    const formErrors = reactive<any>({});

    const loadData = async () => {
      loading.value = true;
      try {
        const r = await ApiService.get(`admin/books/${bookId}`);
        bookTitle.value = r.data.title;
        chapters.value  = r.data.chapters ?? [];
        chapters.value.sort((a: any, b: any) => a.chapter_number - b.chapter_number);
      } catch {
        chapters.value = [];
      }
      loading.value = false;
    };

    const resetForm = () => {
      form.chapter_number = "";
      form.title   = "";
      form.content = "";
      Object.keys(formErrors).forEach(k => delete formErrors[k]);
    };

    const openAdd = () => {
      resetForm();
      editingChapter.value = null;
      // Auto-suggest next chapter number
      if (chapters.value.length > 0) {
        const max = Math.max(...chapters.value.map((c: any) => c.chapter_number));
        form.chapter_number = String(max + 1);
      } else {
        form.chapter_number = "1";
      }
      showModal.value = true;
    };

    const openEdit = (ch: any) => {
      resetForm();
      editingChapter.value = ch;
      form.chapter_number  = ch.chapter_number;
      form.title   = ch.title;
      form.content = ch.content;
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      editingChapter.value = null;
      resetForm();
    };

    const validate = () => {
      Object.keys(formErrors).forEach(k => delete formErrors[k]);
      if (!form.chapter_number) formErrors.chapter_number = "Nomor bab wajib diisi";
      if (!form.title.trim())   formErrors.title   = "Judul wajib diisi";
      if (!form.content.trim()) formErrors.content = "Isi konten wajib diisi";
      return Object.keys(formErrors).length === 0;
    };

    const saveChapter = async () => {
      if (!validate()) return;
      saving.value = true;
      try {
        if (editingChapter.value) {
          // Edit
          const r = await ApiService.put(`admin/chapters/${editingChapter.value.id}`, {
            chapter_number: Number(form.chapter_number),
            title:   form.title,
            content: form.content,
          });
          const idx = chapters.value.findIndex((c: any) => c.id === editingChapter.value.id);
          if (idx !== -1) chapters.value[idx] = r.data;
        } else {
          // Tambah baru
          const r = await ApiService.post(`admin/books/${bookId}/chapters`, {
            chapter_number: Number(form.chapter_number),
            title:   form.title,
            content: form.content,
          });
          chapters.value.push(r.data);
        }
        chapters.value.sort((a: any, b: any) => a.chapter_number - b.chapter_number);
        closeModal();
      } catch (e: any) {
        if (e.response?.data?.errors) {
          Object.assign(formErrors, e.response.data.errors);
        } else if (e.response?.data?.message) {
          formErrors.chapter_number = e.response.data.message;
        }
      }
      saving.value = false;
    };

    const confirmDelete = (ch: any) => { deleteTarget.value = ch; };

    const doDelete = async () => {
      if (!deleteTarget.value) return;
      deleting.value = true;
      try {
        await ApiService.delete(`admin/chapters/${deleteTarget.value.id}`);
        chapters.value = chapters.value.filter((c: any) => c.id !== deleteTarget.value.id);
      } catch {}
      deleting.value = false;
      deleteTarget.value = null;
    };

    onMounted(loadData);

    return { bookId, bookTitle, chapters, loading, saving, deleting, showModal,
             editingChapter, deleteTarget, form, formErrors,
             openAdd, openEdit, closeModal, saveChapter, confirmDelete, doDelete };
  }
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');
* { font-family: 'Outfit', sans-serif; }

.page-container { display: flex; flex-direction: column; gap: 20px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
.page-title { font-size: 22px; font-weight: 800; color: #f0f4ff; margin: 0 0 4px; }
.page-sub { font-size: 13px; color: #4a6080; margin: 0; }
.header-actions { display: flex; gap: 10px; align-items: center; }
.btn-back { padding: 10px 18px; background: #111; border: 1px solid #333; border-radius: 12px; color: #a0aec0; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-back:hover { background: #1a1a1a; color: #e2e8f0; }
.btn-add { display: flex; align-items: center; gap: 8px; background: linear-gradient(135deg,#2b6cb0,#1C325E); color: white; border: none; border-radius: 12px; padding: 11px 20px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); }

.loading-state { display: flex; align-items: center; gap: 12px; padding: 60px; color: #4a6080; justify-content: center; }
.spinner { width: 24px; height: 24px; border: 2px solid #222; border-top-color: #63b3ed; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 80px 20px; background: #111; border: 1px solid #222; border-radius: 18px; }
.empty-icon { font-size: 48px; }
.empty-txt { font-size: 15px; color: #4a6080; font-weight: 600; }

.chapter-list { display: flex; flex-direction: column; gap: 10px; }
.chapter-card { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; background: #111; border: 1px solid #222; border-radius: 16px; transition: all 0.2s; animation: slideUp 0.35s ease both; animation-delay: calc(var(--i) * 0.05s); }
.chapter-card:hover { border-color: #333; background: #161616; }
@keyframes slideUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

.cc-left { display: flex; align-items: center; gap: 16px; flex: 1; }
.cc-num { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg,#2b6cb0,#1C325E); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; color: white; flex-shrink: 0; }
.cc-info { flex: 1; }
.cc-title { font-size: 15px; font-weight: 700; color: #e2e8f0; margin-bottom: 3px; }
.cc-meta { font-size: 12px; color: #4a6080; }

.cc-actions { display: flex; gap: 8px; }
.act-btn { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: 1px solid; font-family: 'Outfit', sans-serif; }
.act-edit { background: rgba(99,179,237,0.08); border-color: rgba(99,179,237,0.2); color: #63b3ed; }
.act-edit:hover { background: rgba(99,179,237,0.15); }
.act-del { background: rgba(229,62,62,0.08); border-color: rgba(229,62,62,0.2); color: #fc8181; }
.act-del:hover { background: rgba(229,62,62,0.15); }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
.modal-box { background: #0d0d0d; border: 1px solid #333; border-radius: 20px; padding: 28px; width: 100%; max-width: 720px; max-height: 90vh; overflow-y: auto; }
.modal-sm { max-width: 400px; text-align: center; }
.modal-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
.modal-title-txt { font-size: 17px; font-weight: 700; color: #e2e8f0; }
.modal-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #333; background: #1a1a1a; color: #a0aec0; cursor: pointer; font-size: 14px; transition: all 0.2s; }
.modal-close:hover { background: #222; }
.modal-icon { font-size: 36px; margin-bottom: 12px; }
.modal-title { font-size: 17px; font-weight: 800; color: #f0f4ff; margin-bottom: 8px; }
.modal-desc { font-size: 13px; color: #718096; line-height: 1.6; margin-bottom: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 12px; }
.modal-actions { display: flex; gap: 10px; justify-content: center; }

.field-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.field-label { font-size: 12px; font-weight: 700; color: #a0aec0; text-transform: uppercase; letter-spacing: 0.5px; }
.field-input, .field-textarea { background: #1a1a1a; border: 1.5px solid #333; border-radius: 12px; padding: 11px 14px; color: #e2e8f0; font-size: 14px; outline: none; transition: border-color 0.2s; width: 100%; font-family: 'Outfit', sans-serif; }
.field-input:focus, .field-textarea:focus { border-color: #63b3ed; background: rgba(99,179,237,0.03); }
.field-input::placeholder, .field-textarea::placeholder { color: #2d4a6a; }
.field-textarea { resize: vertical; }
.field-err { font-size: 11.5px; color: #fc8181; }

.content-toolbar { display: flex; justify-content: flex-end; margin-bottom: 4px; }
.char-count { font-size: 11px; color: #4a6080; font-family: 'JetBrains Mono', monospace; }
.content-area { min-height: 280px; line-height: 1.8; font-size: 14px; }

.btn-cancel { padding: 10px 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
.btn-cancel:hover { background: #222; }
.btn-save { display: flex; align-items: center; gap: 8px; padding: 10px 24px; background: linear-gradient(135deg,#2b6cb0,#1C325E); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-delete { padding: 10px 24px; background: #e53e3e; border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; }
.btn-delete:disabled { opacity: 0.5; cursor: not-allowed; }
.spin-sm { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block; }
</style>