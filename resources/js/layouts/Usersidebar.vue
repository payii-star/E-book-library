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
    <!-- ✅ Logo sama persis admin, badge "User" -->
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
          <span class="brand-badge">User</span>
        </div>
      </router-link>
    </div>

    <!-- Menu -->
    <div class="app-sidebar-menu overflow-hidden flex-column-fluid">
      <div
        id="kt_app_sidebar_menu_wrapper"
        class="app-sidebar-wrapper hover-scroll-overlay-y my-5"
        data-kt-scroll="true"
        data-kt-scroll-activate="true"
        data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu"
        data-kt-scroll-offset="5px"
        data-kt-scroll-save-state="true"
      >
        <div
          class="menu menu-column menu-rounded menu-sub-indention px-3"
          id="kt_app_sidebar_menu"
          data-kt-menu="true"
          data-kt-menu-expand="false"
        >
          <template v-for="(item, i) in UserMenuConfig" :key="i">
            <div v-if="item.heading" class="menu-item pt-5">
              <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">{{ item.heading }}</span>
              </div>
            </div>
            <template v-for="(menuItem, j) in item.pages" :key="j">
              <div class="menu-item">
                <router-link class="menu-link" active-class="active" :to="menuItem.route">
                  <span class="menu-icon">
                    <KTIcon :icon-name="menuItem.keenthemesIcon" icon-class="fs-2" />
                  </span>
                  <span class="menu-title">{{ menuItem.heading }}</span>
                </router-link>
              </div>
            </template>
          </template>

          <!-- Logout -->
          <div class="menu-item pt-5">
            <div class="menu-content">
              <span class="menu-heading fw-bold text-uppercase fs-7">Account</span>
            </div>
          </div>
          <div class="menu-item">
            <a class="menu-link cursor-pointer" @click="onLogout">
              <span class="menu-icon">
                <KTIcon icon-name="exit-right" icon-class="fs-2" />
              </span>
              <span class="menu-title">Logout</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer sidebar -->
    <div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
      <div class="d-flex align-items-center gap-3">
        <div class="symbol symbol-35px">
          <img v-if="authStore.user.avatar" :src="avatarUrl" alt="user" class="rounded-circle object-fit-cover w-35px h-35px" />
          <div v-else class="symbol-label fw-bold bg-primary text-white fs-7 w-35px h-35px rounded-circle d-flex align-items-center justify-content-center">
            {{ authStore.user.name ? authStore.user.name.charAt(0).toUpperCase() : 'U' }}
          </div>
        </div>
        <div class="d-flex flex-column">
          <span class="fw-bold fs-7 text-gray-800">{{ authStore.user.name }}</span>
          <span class="fs-8 text-muted">User</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import UserMenuConfig from "@/layouts/default-layout/config/UserMenuConfig";

export default defineComponent({
  name: "kt-user-sidebar",
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();

    const avatarUrl = computed(() => {
      if (!authStore.user.avatar) return null;
      return `${import.meta.env.VITE_APP_API_URL?.replace('/api', '')}/storage/${authStore.user.avatar}`;
    });

    const onLogout = () => {
      authStore.logout();
      router.push({ name: "user-sign-in" });
    };

    return { authStore, avatarUrl, UserMenuConfig, onLogout };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500&display=swap');

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
</style>