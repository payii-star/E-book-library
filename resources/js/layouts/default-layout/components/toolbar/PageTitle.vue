<template>
  <!--begin::Page title-->
  <div
    v-if="pageTitleDisplay"
    class="page-title-wrapper"
  >
    <template v-if="pageTitle">
      <div class="page-title-card">
        <!-- Decorative dots grid -->
        <div class="dots-grid">
          <span v-for="n in 9" :key="n" class="dot"></span>
        </div>

        <!-- Left accent bar -->
        <div class="accent-bar"></div>

        <!-- Content -->
        <div class="title-content">
          <h1 class="page-heading">{{ pageTitle }}</h1>

          <!-- Breadcrumb -->
          <div v-if="breadcrumbs && pageTitleBreadcrumbDisplay" class="breadcrumb-row">
            <router-link to="/" class="bc-link">Home</router-link>
            <template v-for="(item, i) in breadcrumbs" :key="i">
              <span class="bc-sep">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                  <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
              <span class="bc-item">{{ item }}</span>
            </template>
          </div>
        </div>

        <!-- Right badge -->
        <div class="page-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="9 22 9 12 15 12 15 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>{{ pageTitle }}</span>
        </div>
      </div>
    </template>
  </div>
  <div v-else class="align-items-stretch"></div>
  <!--end::Page title-->
</template>

<script lang="ts">
import { computed, defineComponent } from "vue";
import {
  pageTitleBreadcrumbDisplay,
  pageTitleDirection,
  pageTitleDisplay,
} from "@/layouts/default-layout/config/helper";
import { useRoute } from "vue-router";

export default defineComponent({
  name: "layout-page-title",
  components: {},
  setup() {
    const route = useRoute();
    const pageTitle = computed(() => route.meta.pageTitle);
    const breadcrumbs = computed(() => route.meta.breadcrumbs);

    return {
      pageTitle,
      breadcrumbs,
      pageTitleDisplay,
      pageTitleBreadcrumbDisplay,
      pageTitleDirection,
    };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap');

.page-title-wrapper {
  padding: 0;
  width: 100%;
}

.page-title-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 16px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 14px;
  padding: 14px 20px 14px 22px;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

/* Dots grid decoration */
.dots-grid {
  position: absolute;
  top: 8px;
  right: 110px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 5px;
  pointer-events: none;
  opacity: 0.25;
}
.dot {
  width: 3px;
  height: 3px;
  background: #3b82f6;
  border-radius: 50%;
}

/* Left accent bar */
.accent-bar {
  position: absolute;
  left: 0;
  top: 20%;
  bottom: 20%;
  width: 3px;
  background: linear-gradient(180deg, #3b82f6, #8b5cf6);
  border-radius: 0 3px 3px 0;
}

/* Title content */
.title-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.page-heading {
  font-family: 'Syne', sans-serif;
  font-size: 1.15rem;
  font-weight: 700;
  color: #f1f5f9;
  margin: 0;
  line-height: 1.2;
  letter-spacing: -0.2px;
}

/* Breadcrumb */
.breadcrumb-row {
  display: flex;
  align-items: center;
  gap: 4px;
  flex-wrap: wrap;
}
.bc-link {
  font-size: 11.5px;
  font-weight: 500;
  color: #3b82f6;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
  transition: color 0.15s;
}
.bc-link:hover { color: #60a5fa; }
.bc-sep { color: rgba(255,255,255,0.2); display: flex; align-items: center; }
.bc-item {
  font-size: 11.5px;
  color: #64748b;
  font-family: 'DM Sans', sans-serif;
  font-weight: 400;
}

/* Right badge */
.page-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(59, 130, 246, 0.08);
  border: 1px solid rgba(59, 130, 246, 0.18);
  border-radius: 8px;
  padding: 6px 12px;
  color: #60a5fa;
  font-size: 12px;
  font-weight: 500;
  font-family: 'DM Sans', sans-serif;
  white-space: nowrap;
  flex-shrink: 0;
}
.page-badge svg { opacity: 0.8; }
</style>