<template>
  <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
      <KTHeader />
      <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
        <KTUserSidebar />
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
          <div class="d-flex flex-column flex-column-fluid">
            <KTToolbar />
            <div id="kt_app_content" class="app-content flex-column-fluid">
              <KTContent />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, nextTick, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import KTHeader from "@/layouts/default-layout/components/header/Header.vue";
import KTToolbar from "@/layouts/default-layout/components/toolbar/Toolbar.vue";
import KTContent from "@/layouts/default-layout/components/content/Content.vue";
import KTUserSidebar from "@/layouts/UserSidebar.vue";
import { reinitializeComponents } from "@/core/plugins/keenthemes";
import LayoutService from "@/core/services/LayoutService";

export default defineComponent({
  name: "user-layout",
  components: {
    KTHeader,
    KTToolbar,
    KTContent,
    KTUserSidebar,
  },
  setup() {
    const route = useRoute();

    onMounted(() => {
      LayoutService.init();
      nextTick(() => { reinitializeComponents(); });
    });

    watch(() => route.path, () => {
      nextTick(() => {
        LayoutService.init();
        reinitializeComponents();
      });
    });
  },
});
</script>