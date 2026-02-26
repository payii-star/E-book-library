<template>
  <div class="page-container">

    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">{{ isEdit ? 'Edit Buku' : 'Tambah Buku Baru' }}</h1>
        <p class="page-sub">{{ isEdit ? 'Perbarui informasi buku' : 'Isi detail buku perpustakaan' }}</p>
      </div>
      <router-link to="/admin/books" class="btn-back">← Kembali</router-link>
    </div>

    <div class="form-grid">

      <!-- Left: Book Info -->
      <div class="form-main">
        <div class="form-card">
          <div class="form-card-title">Informasi Buku</div>

          <div class="field-group">
            <label class="field-label">Judul Buku <span class="req">*</span></label>
            <input v-model="form.title" class="field-input" placeholder="Masukkan judul buku"/>
            <span v-if="errors.title" class="field-err">{{ errors.title }}</span>
          </div>

          <div class="field-row">
            <div class="field-group">
              <label class="field-label">Pengarang <span class="req">*</span></label>
              <input v-model="form.author" class="field-input" placeholder="Nama pengarang"/>
              <span v-if="errors.author" class="field-err">{{ errors.author }}</span>
            </div>
            <div class="field-group">
              <label class="field-label">Penerbit</label>
              <input v-model="form.publisher" class="field-input" placeholder="Nama penerbit"/>
            </div>
          </div>

          <div class="field-row">
            <div class="field-group">
              <label class="field-label">Kategori <span class="req">*</span></label>
              <select v-model="form.category_id" class="field-select">
                <option value="">Pilih kategori</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
              <span v-if="errors.category_id" class="field-err">{{ errors.category_id }}</span>
            </div>
            <div class="field-group">
              <label class="field-label">Tahun Terbit</label>
              <input v-model="form.published_year" class="field-input" type="number" placeholder="2024"/>
            </div>
          </div>

          <div class="field-group">
            <label class="field-label">ISBN</label>
            <input v-model="form.isbn" class="field-input" placeholder="978-xxx-xxx-xxx"/>
          </div>

          <div class="field-group">
            <label class="field-label">Sinopsis</label>
            <textarea v-model="form.synopsis" class="field-textarea" rows="5" placeholder="Tulis sinopsis buku..."></textarea>
          </div>

          <div class="field-group">
            <label class="field-label">Status</label>
            <div class="toggle-group">
              <button type="button" :class="['toggle-btn', form.status === 'draft' ? 'toggle-active-warn' : '']" @click="form.status = 'draft'">📝 Draft</button>
              <button type="button" :class="['toggle-btn', form.status === 'published' ? 'toggle-active-green' : '']" @click="form.status = 'published'">✅ Published</button>
            </div>
          </div>
        </div>

        <!-- Chapter Manager (only when editing) -->
        <div v-if="isEdit" class="form-card">
          <div class="form-card-title-row">
            <div class="form-card-title">Kelola Chapter</div>
            <button class="btn-add-chapter" @click="showAddChapter = true">＋ Tambah Chapter</button>
          </div>

          <div v-if="chapters.length === 0" class="chapter-empty">
            Belum ada chapter. Tambahkan chapter pertama!
          </div>

          <div class="chapter-list">
            <div class="chapter-item" v-for="ch in chapters" :key="ch.id">
              <div class="chapter-num">Bab {{ ch.chapter_number }}</div>
              <div class="chapter-info">
                <div class="chapter-title-text">{{ ch.title }}</div>
                <div class="chapter-reads">{{ ch.read_count }} pembaca</div>
              </div>
              <div class="chapter-actions">
                <button class="chap-btn" @click="editChapter(ch)">✏️</button>
                <button class="chap-btn chap-del" @click="deleteChapter(ch.id)">🗑️</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Cover -->
      <div class="form-side">
        <div class="form-card">
          <div class="form-card-title">Cover Buku</div>
          <div class="cover-upload" @click="triggerCoverInput" :style="coverPreview ? {backgroundImage: `url(${coverPreview})`} : {}">
            <div v-if="!coverPreview" class="cover-placeholder">
              <div class="cover-icon">🖼️</div>
              <div class="cover-text">Klik untuk upload cover</div>
              <div class="cover-hint">JPG, PNG, WEBP — Maks 2MB</div>
            </div>
            <div v-else class="cover-overlay">
              <span>Ganti Cover</span>
            </div>
          </div>
          <input ref="coverInput" type="file" class="d-none" accept="image/*" @change="onCoverChange"/>
          <button v-if="coverPreview" class="btn-remove-cover" @click="removeCover">Hapus Cover</button>
        </div>

        <!-- Save Button -->
        <div class="form-card">
          <button class="btn-save w-100" :disabled="saving" @click="saveBook">
            <span v-if="!saving">{{ isEdit ? '💾 Simpan Perubahan' : '📖 Tambah Buku' }}</span>
            <span v-else class="saving-state"><div class="spin-sm"></div> Menyimpan...</span>
          </button>
          <router-link to="/admin/books" class="btn-cancel-full">Batal</router-link>
        </div>
      </div>
    </div>

    <!-- Add/Edit Chapter Modal -->
    <Teleport to="body">
      <div v-if="showAddChapter || editingChapter" class="modal-overlay" @click.self="closeChapterModal">
        <div class="chapter-modal">
          <div class="modal-header-row">
            <div class="modal-title-text">{{ editingChapter ? 'Edit Chapter' : 'Tambah Chapter Baru' }}</div>
            <button class="modal-close-btn" @click="closeChapterModal">✕</button>
          </div>

          <div class="field-group">
            <label class="field-label">Nomor Bab <span class="req">*</span></label>
            <input v-model="chapterForm.chapter_number" class="field-input" type="number" placeholder="1"/>
          </div>

          <div class="field-group">
            <label class="field-label">Judul Bab <span class="req">*</span></label>
            <input v-model="chapterForm.title" class="field-input" placeholder="Judul chapter ini"/>
          </div>

          <div class="field-group">
            <label class="field-label">Isi Konten <span class="req">*</span></label>
            <textarea v-model="chapterForm.content" class="field-textarea chapter-content-area" rows="12" placeholder="Tulis isi bab di sini..."></textarea>
          </div>

          <div class="modal-footer">
            <button class="btn-cancel-sm" @click="closeChapterModal">Batal</button>
            <button class="btn-save-sm" :disabled="savingChapter" @click="saveChapter">
              {{ savingChapter ? 'Menyimpan...' : (editingChapter ? 'Simpan Perubahan' : 'Tambah Chapter') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script lang="ts">
import { defineComponent, ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-book-form",
  setup() {
    const route = useRoute();
    const router = useRouter();
    const isEdit = computed(() => !!route.params.id);

    const categories = ref<any[]>([]);
    const chapters = ref<any[]>([]);
    const saving = ref(false);
    const savingChapter = ref(false);
    const showAddChapter = ref(false);
    const editingChapter = ref<any>(null);
    const coverInput = ref<HTMLInputElement | null>(null);
    const coverPreview = ref<string | null>(null);
    const coverFile = ref<File | null>(null);

    const form = reactive({
      title: "", author: "", publisher: "", category_id: "",
      published_year: "", isbn: "", synopsis: "", status: "draft",
    });

    const chapterForm = reactive({ chapter_number: "", title: "", content: "" });
    const errors = reactive<any>({});

    const triggerCoverInput = () => coverInput.value?.click();
    const onCoverChange = (e: Event) => {
      const f = (e.target as HTMLInputElement).files?.[0];
      if (!f) return;
      coverFile.value = f;
      coverPreview.value = URL.createObjectURL(f);
    };
    const removeCover = () => { coverPreview.value = null; coverFile.value = null; };

    const validate = () => {
      Object.keys(errors).forEach(k => delete errors[k]);
      if (!form.title) errors.title = "Judul wajib diisi";
      if (!form.author) errors.author = "Pengarang wajib diisi";
      if (!form.category_id) errors.category_id = "Kategori wajib dipilih";
      return Object.keys(errors).length === 0;
    };

    const saveBook = async () => {
      if (!validate()) return;
      saving.value = true;
      try {
        const fd = new FormData();
        Object.entries(form).forEach(([k, v]) => { if (v) fd.append(k, v as string); });
        if (coverFile.value) fd.append("cover_image", coverFile.value);

        if (isEdit.value) {
          await ApiService.put(`admin/books/${route.params.id}`, Object.fromEntries(fd));
        } else {
          await ApiService.post("admin/books", fd);
          router.push("/admin/books");
        }
      } catch (e: any) {
        if (e.response?.data?.errors) {
          Object.assign(errors, e.response.data.errors);
        }
      }
      saving.value = false;
    };

    const editChapter = (ch: any) => {
      editingChapter.value = ch;
      chapterForm.chapter_number = ch.chapter_number;
      chapterForm.title = ch.title;
      chapterForm.content = ch.content;
    };

    const closeChapterModal = () => {
      showAddChapter.value = false;
      editingChapter.value = null;
      chapterForm.chapter_number = "";
      chapterForm.title = "";
      chapterForm.content = "";
    };

    const saveChapter = async () => {
      if (!chapterForm.title || !chapterForm.content) return;
      savingChapter.value = true;
      try {
        if (editingChapter.value) {
          const res = await ApiService.put(`admin/chapters/${editingChapter.value.id}`, chapterForm);
          const idx = chapters.value.findIndex(c => c.id === editingChapter.value.id);
          if (idx !== -1) chapters.value[idx] = res.data;
        } else {
          const res = await ApiService.post(`admin/books/${route.params.id}/chapters`, chapterForm);
          chapters.value.push(res.data);
          chapters.value.sort((a,b) => a.chapter_number - b.chapter_number);
        }
        closeChapterModal();
      } catch {}
      savingChapter.value = false;
    };

    const deleteChapter = async (id: number) => {
      if (!confirm("Hapus chapter ini?")) return;
      try {
        await ApiService.delete(`admin/chapters/${id}`);
        chapters.value = chapters.value.filter(c => c.id !== id);
      } catch {}
    };

    const loadData = async () => {
      try {
        const catRes = await ApiService.get("categories");
        categories.value = catRes.data;
      } catch {}

      if (isEdit.value) {
        try {
          const res = await ApiService.get(`admin/books/${route.params.id}`);
          const b = res.data;
          Object.assign(form, { title: b.title, author: b.author, publisher: b.publisher||"",
            category_id: b.category_id, published_year: b.published_year||"",
            isbn: b.isbn||"", synopsis: b.synopsis||"", status: b.status });
          if (b.cover_image) coverPreview.value = `${import.meta.env.VITE_APP_API_URL?.replace('/api','')}/storage/${b.cover_image}`;
          chapters.value = b.chapters ?? [];
        } catch {}
      }
    };

    onMounted(loadData);

    return { isEdit, form, errors, categories, chapters, saving, savingChapter,
             showAddChapter, editingChapter, chapterForm, coverInput, coverPreview,
             triggerCoverInput, onCoverChange, removeCover, saveBook,
             editChapter, closeChapterModal, saveChapter, deleteChapter };
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
.btn-back { display: flex; align-items: center; padding: 10px 18px; background: #111; border: 1px solid #333; border-radius: 12px; color: #a0aec0; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s; }
.btn-back:hover { background: #1a1a1a; color: #e2e8f0; }

.form-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
.form-main { display: flex; flex-direction: column; gap: 20px; }
.form-side { display: flex; flex-direction: column; gap: 16px; }

.form-card { background: #111; border: 1px solid #222; border-radius: 18px; padding: 24px; }
.form-card-title { font-size: 15px; font-weight: 700; color: #e2e8f0; margin-bottom: 20px; }
.form-card-title-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }

.field-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.field-label { font-size: 12px; font-weight: 600; color: #a0aec0; letter-spacing: 0.4px; text-transform: uppercase; }
.req { color: #fc8181; }
.field-input, .field-select, .field-textarea {
  background: #1a1a1a; border: 1.5px solid #333; border-radius: 12px;
  padding: 12px 14px; color: #e2e8f0; font-size: 14px; outline: none;
  transition: border-color 0.2s; width: 100%;
}
.field-input:focus, .field-select:focus, .field-textarea:focus { border-color: #63b3ed; background: rgba(99,179,237,0.05); }
.field-input::placeholder, .field-textarea::placeholder { color: #2d4a6a; }
.field-textarea { resize: vertical; font-family: inherit; }
.field-err { font-size: 11.5px; color: #fc8181; }

.toggle-group { display: flex; gap: 10px; }
.toggle-btn { padding: 10px 20px; border-radius: 10px; border: 1.5px solid #333; background: #1a1a1a; color: #718096; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.toggle-active-warn { background: rgba(246,173,85,0.1); border-color: rgba(246,173,85,0.3); color: #f6ad55; }
.toggle-active-green { background: rgba(104,211,145,0.1); border-color: rgba(104,211,145,0.3); color: #68d391; }

/* Cover */
.cover-upload { height: 220px; border: 2px dashed #333; border-radius: 14px; cursor: pointer; background-size: cover; background-position: center; background-repeat: no-repeat; display: flex; align-items: center; justify-content: center; transition: border-color 0.2s; position: relative; overflow: hidden; }
.cover-upload:hover { border-color: #63b3ed; }
.cover-placeholder { text-align: center; color: #4a6080; }
.cover-icon { font-size: 36px; margin-bottom: 8px; }
.cover-text { font-size: 13px; font-weight: 600; }
.cover-hint { font-size: 11px; margin-top: 4px; }
.cover-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px; opacity: 0; transition: opacity 0.2s; }
.cover-upload:hover .cover-overlay { opacity: 1; }
.btn-remove-cover { margin-top: 10px; width: 100%; padding: 8px; background: rgba(229,62,62,0.1); border: 1px solid rgba(229,62,62,0.2); border-radius: 10px; color: #fc8181; font-size: 13px; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.btn-remove-cover:hover { background: rgba(229,62,62,0.2); }
.d-none { display: none; }

.btn-save { padding: 14px; background: linear-gradient(135deg, #2b6cb0, #1C325E); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; width: 100%; }
.btn-save:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(43,108,176,0.4); }
.btn-save:disabled { opacity: 0.5; cursor: not-allowed; }
.saving-state { display: flex; align-items: center; justify-content: center; gap: 8px; }
.spin-sm { width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.btn-cancel-full { display: block; text-align: center; margin-top: 10px; padding: 10px; background: #1a1a1a; border: 1px solid #333; border-radius: 12px; color: #718096; font-size: 13px; text-decoration: none; transition: all 0.2s; }
.btn-cancel-full:hover { color: #a0aec0; background: #222; }
.w-100 { width: 100%; }

/* Chapter list */
.chapter-empty { text-align: center; padding: 30px; color: #4a6080; font-size: 13px; }
.chapter-list { display: flex; flex-direction: column; gap: 8px; }
.chapter-item { display: flex; align-items: center; gap: 14px; padding: 12px 14px; background: #1a1a1a; border: 1px solid #333; border-radius: 12px; transition: all 0.2s; }
.chapter-item:hover { border-color: #444; background: #222; }
.chapter-num { font-size: 11px; font-weight: 700; color: #2d4a6a; width: 36px; flex-shrink: 0; }
.chapter-info { flex: 1; }
.chapter-title-text { font-size: 13px; font-weight: 600; color: #e2e8f0; }
.chapter-reads { font-size: 11px; color: #4a6080; margin-top: 2px; }
.chapter-actions { display: flex; gap: 6px; }
.chap-btn { width: 30px; height: 30px; border-radius: 8px; border: 1px solid #333; background: #111; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.chap-btn:hover { background: #222; }
.chap-del:hover { background: rgba(229,62,62,0.15); border-color: rgba(229,62,62,0.3); }
.btn-add-chapter { padding: 8px 16px; background: rgba(99,179,237,0.1); border: 1px solid rgba(99,179,237,0.2); border-radius: 10px; color: #63b3ed; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.btn-add-chapter:hover { background: rgba(99,179,237,0.2); }

/* Chapter Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
.chapter-modal { background: #111; border: 1px solid #333; border-radius: 20px; padding: 28px; width: 100%; max-width: 700px; max-height: 90vh; overflow-y: auto; }
.modal-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.modal-title-text { font-size: 17px; font-weight: 700; color: #e2e8f0; }
.modal-close-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #333; background: #1a1a1a; color: #a0aec0; cursor: pointer; font-size: 14px; transition: all 0.2s; }
.modal-close-btn:hover { background: #222; }
.chapter-content-area { min-height: 250px; line-height: 1.8; font-size: 14px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px; }
.btn-cancel-sm { padding: 10px 20px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
.btn-save-sm { padding: 10px 24px; background: linear-gradient(135deg, #2b6cb0, #1C325E); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Outfit', sans-serif; transition: all 0.2s; }
.btn-save-sm:hover:not(:disabled) { transform: translateY(-1px); }
.btn-save-sm:disabled { opacity: 0.5; cursor: not-allowed; }

@media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } .field-row { grid-template-columns: 1fr; } }
</style>