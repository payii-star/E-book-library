<template>
  <div
    id="kt_app_sidebar"
    class="app-sidebar flex-column"
    data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar"
    data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true"
    data-kt-drawer-width="225px"
    data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle"
  >
    <!-- Logo -->
    <div class="sidebar-logo-wrapper" id="kt_app_sidebar_logo">
      <router-link to="/user/dashboard" class="brand-link">
        <div class="brand-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
            <path d="M4 4h7v7H4V4z" fill="currentColor" opacity="0.9"/>
            <path d="M13 4h7v7h-7V4z" fill="currentColor" opacity="0.45"/>
            <path d="M4 13h7v7H4v-7z" fill="currentColor" opacity="0.45"/>
            <path d="M13 13h7v7h-7v-7z" fill="currentColor" opacity="0.75"/>
          </svg>
        </div>
        <div class="brand-text">
          <span class="brand-name">E-Library</span>
        </div>
      </router-link>
    </div>

    <!-- Menu -->
    <div class="sidebar-menu-wrapper">
      <div class="sidebar-scroll-area">
        <nav class="sidebar-nav">
          <template v-for="(item, i) in UserMenuConfig" :key="i">
            <!-- Section heading -->
            <div v-if="item.heading" class="nav-section-label">
              {{ item.heading }}
            </div>
            <!-- Menu items -->
            <template v-for="(menuItem, j) in item.pages" :key="j">
              <router-link
                class="nav-item"
                active-class="is-active"
                :to="menuItem.route"
                :style="{ animationDelay: `${(i + j) * 55}ms` }"
              >
                <span class="nav-icon">
                  <KTIcon :icon-name="menuItem.keenthemesIcon" icon-class="fs-5" />
                </span>
                <span class="nav-label">{{ menuItem.heading }}</span>
                <span class="active-pip"></span>
              </router-link>
            </template>
          </template>
        </nav>
      </div>
    </div>

    <!-- Footer -->
    <div class="sidebar-footer" id="kt_app_sidebar_footer">
      <!-- Logout -->
      <button class="logout-btn" @click="onLogout">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"
            stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Logout</span>
      </button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import UserMenuConfig from "@/layouts/default-layout/config/UserMenuConfig";

export default defineComponent({
  name: "kt-user-sidebar",
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();

    const onLogout = () => {
      authStore.logout();
      router.push({ name: "user-sign-in" });
    };

    return { UserMenuConfig, onLogout };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap');

/* ===== LOGO ===== */
.sidebar-logo-wrapper {
  display: flex;
  align-items: center;
  padding: 0 18px;
  height: 68px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  flex-shrink: 0;
}
.brand-link {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}
.brand-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: linear-gradient(135deg, rgba(59,130,246,0.28) 0%, rgba(99,102,241,0.28) 100%);
  border: 1px solid rgba(59,130,246,0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  flex-shrink: 0;
  transition: all 0.2s ease;
}
.brand-link:hover .brand-icon {
  background: linear-gradient(135deg, rgba(59,130,246,0.4) 0%, rgba(99,102,241,0.4) 100%);
  border-color: rgba(59,130,246,0.55);
  box-shadow: 0 0 16px rgba(59,130,246,0.2);
}
.brand-text {
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}
.brand-name {
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 16px;
  color: #f1f5f9;
  letter-spacing: -0.3px;
}
.brand-badge {
  font-family: 'DM Sans', sans-serif;
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #34d399;
  background: rgba(52,211,153,0.12);
  border: 1px solid rgba(52,211,153,0.25);
  border-radius: 5px;
  padding: 2px 6px;
}

/* ===== MENU WRAPPER ===== */
.sidebar-menu-wrapper {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  padding: 8px 0;
}
.sidebar-scroll-area {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 8px 12px 16px;
  scrollbar-width: none;
}
.sidebar-scroll-area::-webkit-scrollbar { display: none; }

.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

/* Section label */
.nav-section-label {
  font-family: 'DM Sans', sans-serif;
  font-size: 10.5px;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #334155;
  padding: 14px 12px 6px;
}

/* Nav item */
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: 11px;
  color: #64748b;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 13.5px;
  font-weight: 400;
  cursor: pointer;
  border: none;
  background: transparent;
  width: 100%;
  text-align: left;
  position: relative;
  transition: background 0.18s ease, color 0.18s ease;
  white-space: nowrap;
  overflow: hidden;
  animation: nav-in 0.35s ease both;
}
@keyframes nav-in {
  from { opacity: 0; transform: translateX(-8px); }
  to   { opacity: 1; transform: translateX(0); }
}
.nav-item:hover {
  background: rgba(255, 255, 255, 0.04);
  color: #cbd5e1;
}
.nav-item.is-active {
  background: linear-gradient(135deg, rgba(59,130,246,0.16) 0%, rgba(59,130,246,0.06) 100%);
  color: #93c5fd;
}
.active-pip {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #3b82f6;
  box-shadow: 0 0 7px rgba(59,130,246,0.85);
  flex-shrink: 0;
  margin-left: auto;
  opacity: 0;
  transition: opacity 0.15s;
}
.nav-item.is-active .active-pip { opacity: 1; }

.nav-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: inherit;
}
.nav-label { flex: 1; line-height: 1.3; }

/* ===== FOOTER ===== */
.sidebar-footer {
  padding: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  flex-shrink: 0;
}
.logout-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 9px 12px;
  border-radius: 10px;
  background: transparent;
  border: 1px solid rgba(255,255,255,0.07);
  color: #64748b;
  font-size: 13.5px;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  transition: all 0.2s ease;
}
.logout-btn:hover {
  background: rgba(239,68,68,0.1);
  border-color: rgba(239,68,68,0.25);
  color: #fca5a5;
}
</style>