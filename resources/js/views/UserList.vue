<template>
  <div class="page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Data Anggota</h1>
        <p class="page-sub">{{ totalUsers }} anggota terdaftar</p>
      </div>
    </div>

    <div class="filter-bar">
      <div class="search-wrap">
        <svg class="search-ico" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="search" class="search-input" placeholder="Cari nama atau email..." @input="onSearch"/>
      </div>
    </div>

    <div class="table-card">
      <div v-if="loading" class="table-loading">
        <div class="spinner"></div><span>Memuat data anggota...</span>
      </div>
      <table v-else class="data-table">
        <thead>
          <tr>
            <th>ANGGOTA</th>
            <th>EMAIL</th>
            <th>TELEPON</th>
            <th>BERGABUNG</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="users.length === 0">
            <td colspan="5">
              <div class="empty-state">
                <div class="empty-icon">👥</div>
                <div>Belum ada anggota terdaftar</div>
              </div>
            </td>
          </tr>
          <tr v-for="user in users" :key="user.id" class="table-row">
            <td>
              <div class="user-cell">
                <div class="user-avatar" :style="{background: avatarGrad(user.id)}">
                  <img v-if="user.avatar" :src="getAvatarUrl(user.avatar)" class="avatar-img"/>
                  <span v-else>{{ user.name?.charAt(0).toUpperCase() }}</span>
                </div>
                <div>
                  <div class="user-name">{{ user.name }}</div>
                  <div class="user-role">{{ user.role }}</div>
                </div>
              </div>
            </td>
            <td class="cell-muted">{{ user.email }}</td>
            <td class="cell-muted">{{ user.phone || '—' }}</td>
            <td class="cell-muted">{{ formatDate(user.created_at) }}</td>
            <td>
              <span class="badge-active">● Aktif</span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="totalPages > 1" class="pagination">
        <button class="page-btn" :disabled="currentPage === 1" @click="goPage(currentPage - 1)">← Prev</button>
        <span class="page-info">{{ currentPage }} / {{ totalPages }}</span>
        <button class="page-btn" :disabled="currentPage === totalPages" @click="goPage(currentPage + 1)">Next →</button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import ApiService from "@/core/services/ApiService";

export default defineComponent({
  name: "admin-user-list",
  setup() {
    const users = ref<any[]>([]);
    const loading = ref(true);
    const search = ref("");
    const currentPage = ref(1);
    const totalPages = ref(1);
    const totalUsers = ref(0);
    let searchTimer: any = null;

    const gradients = ["linear-gradient(135deg,#3182ce,#2c5282)","linear-gradient(135deg,#38a169,#276749)","linear-gradient(135deg,#d69e2e,#b7791f)","linear-gradient(135deg,#805ad5,#553c9a)","linear-gradient(135deg,#e53e3e,#9b2335)"];
    const avatarGrad = (id: number) => gradients[id % gradients.length];

    const getAvatarUrl = (path: string) => `${import.meta.env.VITE_APP_API_URL?.replace('/api','')}/storage/${path}`;

    const formatDate = (d: string) => {
      if (!d) return "—";
      return new Date(d).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });
    };

    const load = async (page = 1) => {
      loading.value = true;
      try {
        const res = await ApiService.query("admin/users", { params: { page, per_page: 15, search: search.value || undefined } });
        users.value = res.data.data ?? res.data;
        totalPages.value = res.data.last_page ?? 1;
        totalUsers.value = res.data.total ?? users.value.length;
        currentPage.value = page;
      } catch {
        users.value = [];
        totalUsers.value = 0;
      }
      loading.value = false;
    };

    const onSearch = () => { clearTimeout(searchTimer); searchTimer = setTimeout(() => load(1), 400); };
    const goPage = (p: number) => load(p);

    onMounted(() => load());
    return { users, loading, search, currentPage, totalPages, totalUsers, avatarGrad, getAvatarUrl, formatDate, onSearch, goPage };
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
.filter-bar { display: flex; gap: 12px; }
.search-wrap { position: relative; flex: 1; max-width: 400px; }
.search-ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #4a6080; }
.search-input { width: 100%; padding: 10px 16px 10px 40px; background: #111; border: 1.5px solid #222; border-radius: 12px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color 0.2s; }
.search-input:focus { border-color: #63b3ed; }
.search-input::placeholder { color: #2d4a6a; }
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
.empty-state { display: flex; flex-direction: column; align-items: center; gap: 10px; color: #4a6080; padding: 50px; }
.empty-icon { font-size: 36px; }
.user-cell { display: flex; align-items: center; gap: 12px; }
.user-avatar { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 800; color: white; flex-shrink: 0; overflow: hidden; }
.avatar-img { width: 100%; height: 100%; object-fit: cover; }
.user-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
.user-role { font-size: 11px; color: #4a6080; text-transform: capitalize; margin-top: 1px; }
.cell-muted { color: #718096; font-size: 13px; }
.badge-active { font-size: 11px; font-weight: 600; color: #68d391; }
.pagination { display: flex; align-items: center; justify-content: center; gap: 16px; padding: 16px; border-top: 1px solid #222; }
.page-btn { padding: 8px 16px; background: #1a1a1a; border: 1px solid #333; border-radius: 10px; color: #a0aec0; font-size: 13px; cursor: pointer; transition: all 0.2s; font-family: 'Outfit', sans-serif; }
.page-btn:hover:not(:disabled) { background: #222; color: #e2e8f0; }
.page-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.page-info { font-size: 13px; color: #4a6080; font-family: 'JetBrains Mono', monospace; }
</style>