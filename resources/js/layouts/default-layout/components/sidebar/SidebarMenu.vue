<template>
  <div class="sidebar-menu-wrapper">
    <div class="sidebar-scroll-area">
      <nav class="sidebar-nav">
        <template v-for="(item, i) in MainMenuConfig" :key="i">
          <template v-for="(menuItem, j) in item.pages" :key="j">

            <!-- SINGLE LINK -->
            <router-link
              v-if="menuItem.heading && menuItem.route"
              class="nav-item"
              active-class="is-active"
              :to="menuItem.route"
              :style="{ animationDelay: `${(i + j) * 55}ms` }"
            >
              <span class="nav-icon">
                <i :class="`bi ${menuItem.fontIcon} fs-5`"></i>
              </span>
              <span class="nav-label">{{ translate(menuItem.heading) }}</span>
              <span class="active-pip"></span>
            </router-link>

            <!-- ACCORDION SECTION -->
            <div
              v-if="menuItem.sectionTitle && menuItem.route"
              class="nav-section"
              :style="{ animationDelay: `${(i + j) * 55}ms` }"
            >
              <button
                class="nav-item nav-trigger"
                :class="{
                  'is-open': openSections[menuItem.route],
                  'is-active-section': hasActiveChildren(menuItem.route),
                }"
                @click="toggleSection(menuItem.route)"
              >
                <span class="nav-icon">
                  <i :class="`bi ${menuItem.fontIcon} fs-5`"></i>
                </span>
                <span class="nav-label">{{ translate(menuItem.sectionTitle) }}</span>
                <span class="chevron" :class="{ rotated: openSections[menuItem.route] }">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>
                <span v-if="hasActiveChildren(menuItem.route)" class="active-pip"></span>
              </button>

              <!-- Sub items -->
              <div
                class="sub-menu"
                :class="{ 'sub-open': openSections[menuItem.route] }"
              >
                <div class="sub-menu-inner">
                  <div class="sub-track"></div>
                  <template v-for="(sub, k) in menuItem.sub" :key="k">
                    <router-link
                      v-if="sub.heading && sub.route"
                      class="sub-item"
                      active-class="sub-active"
                      :to="sub.route"
                    >
                      <span class="sub-dot"></span>
                      <span>{{ translate(sub.heading) }}</span>
                    </router-link>
                  </template>
                </div>
              </div>
            </div>

          </template>
        </template>
      </nav>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive, watch } from "vue";
import { useRoute } from "vue-router";
import MainMenuConfig from "@/layouts/default-layout/config/MainMenuConfig";
import { useI18n } from "vue-i18n";

export default defineComponent({
  name: "sidebar-menu",
  setup() {
    const { t, te } = useI18n();
    const route = useRoute();

    const translate = (text: string) => (te(text) ? t(text) : text);

    const hasActiveChildren = (match: string) => route.path.includes(match);

    // Build open state from config
    const openSections = reactive<Record<string, boolean>>({});

    MainMenuConfig.forEach((group: any) => {
      group.pages.forEach((page: any) => {
        if (page.sectionTitle && page.route) {
          openSections[page.route] = route.path.includes(page.route);
        }
      });
    });

    // Auto-open section when navigating
    watch(
      () => route.path,
      (newPath) => {
        MainMenuConfig.forEach((group: any) => {
          group.pages.forEach((page: any) => {
            if (page.sectionTitle && page.route && newPath.includes(page.route)) {
              openSections[page.route] = true;
            }
          });
        });
      }
    );

    const toggleSection = (key: string) => {
      openSections[key] = !openSections[key];
    };

    return {
      MainMenuConfig,
      openSections,
      translate,
      hasActiveChildren,
      toggleSection,
    };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&display=swap');

/* ===== WRAPPER ===== */
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

/* ===== NAV ===== */
.sidebar-nav {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

/* ===== NAV ITEM (single & trigger) ===== */
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

/* Active state for router-link */
.nav-item.is-active {
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.16) 0%, rgba(59, 130, 246, 0.06) 100%);
  color: #93c5fd;
}

/* Active pip — only visible when is-active */
.active-pip {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #3b82f6;
  box-shadow: 0 0 7px rgba(59, 130, 246, 0.85);
  flex-shrink: 0;
  margin-left: auto;
  opacity: 0;
  transition: opacity 0.15s;
}
.nav-item.is-active .active-pip { opacity: 1; }

/* Section trigger open / active-section */
.nav-trigger.is-open,
.nav-trigger.is-active-section {
  color: #93c5fd;
}
.nav-trigger.is-open {
  background: rgba(255, 255, 255, 0.03);
}

/* ===== ICON ===== */
.nav-icon {
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: inherit;
}

/* ===== LABEL ===== */
.nav-label { flex: 1; line-height: 1.3; }

/* ===== CHEVRON ===== */
.chevron {
  flex-shrink: 0;
  color: #475569;
  transition: transform 0.25s ease;
  display: flex;
  align-items: center;
}
.chevron.rotated { transform: rotate(180deg); }

/* ===== SUBMENU ACCORDION ===== */
.sub-menu {
  display: grid;
  grid-template-rows: 0fr;
  transition: grid-template-rows 0.28s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}
.sub-menu.sub-open { grid-template-rows: 1fr; }

.sub-menu-inner {
  min-height: 0;
  overflow: hidden;
  position: relative;
  padding-left: 30px;
  padding-top: 3px;
  padding-bottom: 3px;
}

/* Vertical connector line */
.sub-track {
  position: absolute;
  left: 19px;
  top: 3px;
  bottom: 3px;
  width: 1px;
  background: linear-gradient(
    to bottom,
    rgba(59, 130, 246, 0.35),
    rgba(59, 130, 246, 0.04)
  );
  border-radius: 1px;
}

/* ===== SUB ITEM ===== */
.sub-item {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 7px 10px;
  border-radius: 9px;
  color: #475569;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 400;
  transition: all 0.15s ease;
  white-space: nowrap;
}
.sub-item:hover {
  color: #cbd5e1;
  background: rgba(255, 255, 255, 0.04);
}
.sub-item.sub-active {
  color: #93c5fd;
  background: rgba(59, 130, 246, 0.1);
}

.sub-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.15);
  flex-shrink: 0;
  transition: all 0.2s;
}
.sub-item.sub-active .sub-dot {
  background: #3b82f6;
  box-shadow: 0 0 6px rgba(59, 130, 246, 0.7);
}
</style>