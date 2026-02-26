<template>
  <div class="sidebar-logo-wrapper" id="kt_app_sidebar_logo">

    <router-link to="/" class="brand-link">
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
        <span class="brand-badge">Admin</span>
      </div>
    </router-link>

  </div>
</template>

<script setup lang="ts">
// intentionally empty
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
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.28) 0%, rgba(99, 102, 241, 0.28) 100%);
  border: 1px solid rgba(59, 130, 246, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #60a5fa;
  flex-shrink: 0;
  transition: all 0.2s ease;
}
.brand-link:hover .brand-icon {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.4) 0%, rgba(99, 102, 241, 0.4) 100%);
  border-color: rgba(59, 130, 246, 0.55);
  box-shadow: 0 0 16px rgba(59, 130, 246, 0.2);
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
  color: #60a5fa;
  background: rgba(59, 130, 246, 0.12);
  border: 1px solid rgba(59, 130, 246, 0.25);
  border-radius: 5px;
  padding: 2px 6px;
}
</style>