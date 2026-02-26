<template>
  <div
    v-if="headerDisplay"
    id="kt_app_header"
    class="app-header"
  >
    <div class="glow-orb glow-left"></div>
    <div class="glow-orb glow-right"></div>

    <div
      class="app-container d-flex align-items-center justify-content-between"
      :class="{
        'container-fluid': headerWidthFluid,
        'container-xxl': !headerWidthFluid,
      }"
    >

      <!-- LEFT: Mobile Toggle only -->
      <div class="d-flex align-items-center">
        <div
          class="d-flex align-items-center d-lg-none"
          id="kt_app_sidebar_mobile_toggle"
        >
          <button class="mobile-toggle-btn">
            <span class="bar bar-1"></span>
            <span class="bar bar-2"></span>
            <span class="bar bar-3"></span>
          </button>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="d-flex align-items-center gap-3">

        <!-- Status indicator -->
        <div class="status-pill d-none d-md-flex align-items-center gap-2">
          <span class="status-dot"></span>
          <span class="status-text">System Online</span>
        </div>

        <div class="header-divider d-none d-md-block"></div>

        <!-- USER DROPDOWN -->
        <div
          class="user-trigger d-flex align-items-center gap-2 cursor-pointer"
          data-kt-menu-trigger="click"
          data-kt-menu-attach="parent"
          data-kt-menu-placement="bottom-end"
        >
          <div class="avatar-ring">
            <div class="avatar-label">{{ initials }}</div>
          </div>
          <div class="d-none d-sm-block">
            <div class="user-name">{{ authStore.user.name }}</div>
          </div>
          <svg class="chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>

        <!-- Dropdown -->
        <div
          class="menu menu-sub menu-sub-dropdown menu-rounded menu-state-primary fw-semibold fs-6 dropdown-panel"
          data-kt-menu="true"
        >
          <div class="dropdown-profile px-5 py-4">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar-ring avatar-lg">
                <div class="avatar-label">{{ initials }}</div>
              </div>
              <div>
                <div class="dropdown-name">{{ authStore.user.name }}</div>
                <div class="dropdown-role">Administrator</div>
              </div>
            </div>
          </div>

          <div class="dropdown-sep"></div>

          <div class="dropdown-items px-3 pb-3">
            <a class="dropdown-item" @click="logout">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <span>Sign Out</span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import {
  headerDisplay,
  headerWidthFluid,
} from "@/layouts/default-layout/config/helper";

export default defineComponent({
  name: "layout-header",
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();

    const initials = computed(() => {
      if (!authStore.user.name) return "A";
      return authStore.user.name
        .split(" ")
        .map((n: string) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
    });

    const logout = () => {
      authStore.logout();
      router.push("/sign-in");
    };

    return {
      headerDisplay,
      headerWidthFluid,
      authStore,
      initials,
      logout,
    };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

.app-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  height: 64px;
  background: rgba(8, 10, 18, 0.75);
  backdrop-filter: blur(20px) saturate(180%);
  -webkit-backdrop-filter: blur(20px) saturate(180%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  font-family: 'DM Sans', sans-serif;
  overflow: hidden;
}

.app-container {
  height: 100%;
  padding: 0 24px;
  position: relative;
  z-index: 2;
}

.glow-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  pointer-events: none;
  z-index: 0;
}
.glow-left {
  width: 200px;
  height: 100px;
  background: rgba(59, 130, 246, 0.08);
  top: -30px;
  left: 40%;
}
.glow-right {
  width: 160px;
  height: 80px;
  background: rgba(139, 92, 246, 0.06);
  top: -20px;
  right: 120px;
}

/* Mobile toggle */
.mobile-toggle-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  width: 38px;
  height: 38px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.mobile-toggle-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.18);
}
.bar {
  display: block;
  height: 1.5px;
  background: rgba(255, 255, 255, 0.75);
  border-radius: 2px;
}
.bar-1 { width: 16px; }
.bar-2 { width: 12px; }
.bar-3 { width: 14px; }

/* Status pill */
.status-pill {
  background: rgba(16, 185, 129, 0.08);
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 20px;
  padding: 5px 12px;
}
.status-dot {
  width: 6px;
  height: 6px;
  background: #10b981;
  border-radius: 50%;
  box-shadow: 0 0 6px rgba(16, 185, 129, 0.8);
  animation: pulse-dot 2.5s ease-in-out infinite;
}
@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
.status-text {
  font-size: 11.5px;
  font-weight: 500;
  color: #6ee7b7;
  letter-spacing: 0.3px;
}

.header-divider {
  width: 1px;
  height: 28px;
  background: rgba(255, 255, 255, 0.08);
}

/* User trigger */
.user-trigger {
  padding: 5px 10px 5px 5px;
  border-radius: 40px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.04);
  transition: all 0.2s ease;
}
.user-trigger:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.14);
}

/* Avatar */
.avatar-ring {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  flex-shrink: 0;
}
.avatar-ring::before {
  content: '';
  position: absolute;
  inset: -2px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.5), rgba(139, 92, 246, 0.5));
  z-index: -1;
}
.avatar-label {
  font-family: 'Syne', sans-serif;
  font-weight: 700;
  font-size: 13px;
  color: #fff;
  letter-spacing: 0.3px;
}
.avatar-lg { width: 42px; height: 42px; }
.avatar-lg .avatar-label { font-size: 15px; }

.user-name {
  font-size: 13px;
  font-weight: 500;
  color: #e2e8f0;
  line-height: 1;
}

.chevron-icon {
  color: rgba(255, 255, 255, 0.35);
  transition: transform 0.2s ease;
}

/* Dropdown */
.dropdown-panel {
  background: rgba(10, 13, 25, 0.95) !important;
  backdrop-filter: blur(24px) !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  border-radius: 16px !important;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.04) !important;
  min-width: 230px !important;
  overflow: hidden;
  padding: 0 !important;
  margin-top: 10px !important;
}
.dropdown-profile { background: rgba(255, 255, 255, 0.03); }
.dropdown-name {
  font-family: 'Syne', sans-serif;
  font-weight: 600;
  font-size: 14px;
  color: #f1f5f9;
  line-height: 1.3;
}
.dropdown-role {
  font-size: 11px;
  color: #60a5fa;
  font-weight: 500;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  margin-top: 2px;
}
.dropdown-sep {
  height: 1px;
  background: rgba(255, 255, 255, 0.06);
  margin: 0;
}
.dropdown-items { padding-top: 8px; }
.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  color: #94a3b8;
  font-size: 13.5px;
  font-weight: 400;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.15s ease;
}
.dropdown-item:hover { background: rgba(239, 68, 68, 0.1); color: #fca5a5; }
.dropdown-item svg { flex-shrink: 0; opacity: 0.7; }
.dropdown-item:hover svg { opacity: 1; }

.gap-2 { gap: 8px !important; }
.gap-3 { gap: 12px !important; }
</style>