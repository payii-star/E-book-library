<template>
  <div v-if="footerDisplay" id="kt_app_footer" class="app-footer">
    <div
      class="footer-container"
      :class="{
        'container-fluid': footerWidthFluid,
        'container-xxl': !footerWidthFluid,
      }"
    >
      <!-- Left: Copyright -->
      <div class="footer-left">
        <div class="footer-logo">
          <div class="footer-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
              <path d="M4 4h7v7H4V4z" fill="currentColor" opacity="0.9"/>
              <path d="M13 4h7v7h-7V4z" fill="currentColor" opacity="0.45"/>
              <path d="M4 13h7v7H4v-7z" fill="currentColor" opacity="0.45"/>
              <path d="M13 13h7v7h-7v-7z" fill="currentColor" opacity="0.75"/>
            </svg>
          </div>
          <span class="footer-brand">E-Library</span>
        </div>
        <span class="footer-copy">© {{ year }} Sistem Manajemen Perpustakaan</span>
      </div>

      <!-- Right: Links -->
      <div class="footer-right">
        <span class="footer-made"></span>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from "vue";
import { footerDisplay, footerWidthFluid } from "@/layouts/default-layout/config/helper";

export default defineComponent({
  name: "theme-footer",
  setup() {
    const year = computed(() => new Date().getFullYear());
    return { footerWidthFluid, footerDisplay, year };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap');

.app-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  background: rgba(8, 10, 18, 0.6);
  backdrop-filter: blur(10px);
  font-family: 'DM Sans', sans-serif;
}

.footer-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 24px;
  gap: 12px;
}

.footer-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 7px;
}

.footer-icon {
  width: 26px;
  height: 26px;
  border-radius: 7px;
  background: linear-gradient(135deg, rgba(59,130,246,0.25), rgba(99,102,241,0.25));
  border: 1px solid rgba(59,130,246,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
}

.footer-brand {
  font-size: 13px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.2px;
}

.footer-copy {
  font-size: 12px;
  color: #ffffff;
  padding-left: 14px;
  border-left: 1px solid rgba(255,255,255,0.07);
}

.footer-right { display: flex; align-items: center; }

.footer-made {
  font-size: 12px;
  color: #334155;
}

@media (max-width: 640px) {
  .footer-container { flex-direction: column; align-items: flex-start; gap: 6px; }
  .footer-copy { display: none; }
}
</style>