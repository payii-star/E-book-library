<template>
  <!--begin::Authentication Layout -->
  <div class="d-flex flex-column flex-lg-row flex-column-fluid">
    <!--begin::Body (Left)-->
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
      <div class="d-flex flex-center flex-column flex-lg-row-fluid">
        <div class="w-lg-500px p-10">
          <router-view></router-view>
        </div>
      </div>
    </div>
    <!--end::Body-->

    <!--begin::Aside (Right) - Blue Crystal -->
    <div class="d-flex flex-lg-row-fluid w-lg-50 order-1 order-lg-2 crystal-aside">

      <!-- Light beams -->
      <div class="light-beam beam-1"></div>
      <div class="light-beam beam-2"></div>
      <div class="light-beam beam-3"></div>

      <!-- Floating crystals -->
      <div class="crystal crystal-1"></div>
      <div class="crystal crystal-2"></div>
      <div class="crystal crystal-3"></div>
      <div class="crystal crystal-4"></div>
      <div class="crystal crystal-5"></div>

      <!-- Particles -->
      <div class="particle p-1"></div>
      <div class="particle p-2"></div>
      <div class="particle p-3"></div>
      <div class="particle p-4"></div>
      <div class="particle p-5"></div>
      <div class="particle p-6"></div>

      <!--begin::Content-->
      <div class="d-flex flex-column flex-center py-15 px-10 w-100 position-relative" style="z-index:2">

        <!-- Logo -->
        <router-link to="/" class="mb-14">
          <img alt="Logo" :src="getAssetPath('media/logos/custom-1.png')" class="h-50px h-lg-65px logo-glow"/>
        </router-link>

        <!-- Main glass card -->
        <div class="main-glass-card mb-12">
          <div class="glass-shine"></div>

          <!-- Icon row -->
          <div class="icon-row mb-6">
            <div class="glass-icon gi-blue">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#63b3ed" stroke-width="1.8">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
              </svg>
            </div>
            <div class="glass-icon gi-teal">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#4fd1c5" stroke-width="1.8">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <div class="glass-icon gi-indigo">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7f9cf5" stroke-width="1.8">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </div>
          </div>

          <h2 class="glass-title mb-3">Selamat Datang</h2>
          <p class="glass-desc mb-8">Platform modern dengan keamanan tingkat tinggi.<br/>Data kamu terlindungi sepenuhnya.</p>

          <!-- Stats -->
          <div class="stats-row">
            <div class="stat-item">
              <div class="stat-value" style="color:#63b3ed">256-bit</div>
              <div class="stat-label">Enkripsi</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value" style="color:#4fd1c5">99.9%</div>
              <div class="stat-label">Uptime</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
              <div class="stat-value" style="color:#7f9cf5">24/7</div>
              <div class="stat-label">Support</div>
            </div>
          </div>
        </div>

        <!-- Feature pills -->
        <div class="feature-row">
          <div class="feature-pill">
            <span class="pill-dot" style="background:#63b3ed;box-shadow:0 0 8px #63b3ed"></span>
            Aman & Terpercaya
          </div>
          <div class="feature-pill">
            <span class="pill-dot" style="background:#4fd1c5;box-shadow:0 0 8px #4fd1c5"></span>
            Enkripsi End-to-End
          </div>
          <div class="feature-pill">
            <span class="pill-dot" style="background:#7f9cf5;box-shadow:0 0 8px #7f9cf5"></span>
            Privasi Terjaga
          </div>
        </div>

      </div>
      <!--end::Content-->
    </div>
    <!--end::Aside-->
  </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, onMounted } from "vue";
import LayoutService from "@/core/services/LayoutService";
import { useBodyStore } from "@/stores/body";

export default defineComponent({
  name: "auth-layout",
  components: {},
  setup() {
    const store = useBodyStore();
    onMounted(() => {
      LayoutService.emptyElementClassesAndAttributes(document.body);
      store.addBodyClassname("app-blank");
      store.addBodyClassname("bg-body");
    });
    return { getAssetPath };
  },
});
</script>

<style scoped>
.crystal-aside {
  background: linear-gradient(145deg, #0d1b2a 0%, #1C325E 50%, #0f2339 100%);
  position: relative;
  overflow: hidden;
}

/* Light beams */
.light-beam {
  position: absolute;
  pointer-events: none;
  animation: beamPulse ease-in-out infinite;
}
.beam-1 {
  width: 300px; height: 600px;
  background: linear-gradient(180deg, rgba(49,130,206,0.15) 0%, transparent 100%);
  top: -100px; left: 30%;
  transform: translateX(-50%) rotate(-20deg);
  animation-duration: 6s;
}
.beam-2 {
  width: 200px; height: 500px;
  background: linear-gradient(180deg, rgba(79,209,197,0.1) 0%, transparent 100%);
  top: -80px; left: 65%;
  transform: translateX(-50%) rotate(15deg);
  animation-duration: 8s; animation-delay: 2s;
}
.beam-3 {
  width: 250px; height: 450px;
  background: linear-gradient(180deg, rgba(99,179,237,0.08) 0%, transparent 100%);
  top: -60px; left: 10%;
  transform: translateX(-50%) rotate(5deg);
  animation-duration: 10s; animation-delay: 4s;
}
@keyframes beamPulse { 0%,100%{opacity:0} 50%{opacity:1} }

/* Crystals */
.crystal {
  position: absolute;
  animation: crystalFloat ease-in-out infinite;
}
@keyframes crystalFloat {
  0%,100% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
  50% { transform: translateY(-20px) rotate(8deg); opacity: 0.2; }
}
.crystal-1 {
  width: 140px; height: 140px;
  background: linear-gradient(135deg, rgba(49,130,206,0.7), rgba(99,179,237,0.3));
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
  top: 40px; right: 30px; animation-duration: 7s;
  filter: blur(0.5px);
}
.crystal-2 {
  width: 90px; height: 90px;
  background: linear-gradient(135deg, rgba(79,209,197,0.6), rgba(99,179,237,0.3));
  clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
  bottom: 80px; left: 30px; animation-duration: 9s; animation-delay: 1.5s;
  filter: blur(0.5px);
}
.crystal-3 {
  width: 60px; height: 60px;
  background: linear-gradient(135deg, rgba(99,179,237,0.6), rgba(49,130,206,0.3));
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
  top: 200px; left: 50px; animation-duration: 5s; animation-delay: 0.5s;
  filter: blur(0.3px);
}
.crystal-4 {
  width: 50px; height: 50px;
  background: linear-gradient(135deg, rgba(79,209,197,0.5), rgba(99,179,237,0.3));
  clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
  bottom: 200px; right: 60px; animation-duration: 6s; animation-delay: 3s;
  filter: blur(0.3px);
}
.crystal-5 {
  width: 75px; height: 75px;
  background: linear-gradient(135deg, rgba(127,156,245,0.5), rgba(79,209,197,0.3));
  clip-path: polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%);
  top: 60%; right: 20px; animation-duration: 8s; animation-delay: 2s;
  filter: blur(0.3px);
}

/* Particles */
.particle { position: absolute; border-radius: 50%; animation: particleDrift ease-in-out infinite; }
@keyframes particleDrift {
  0%,100%{transform:translate(0,0);opacity:0.4}
  33%{transform:translate(10px,-15px);opacity:0.8}
  66%{transform:translate(-8px,8px);opacity:0.3}
}
.p-1{width:4px;height:4px;background:#63b3ed;top:15%;left:20%;animation-duration:5s}
.p-2{width:3px;height:3px;background:#4fd1c5;top:35%;right:15%;animation-duration:7s;animation-delay:1s}
.p-3{width:5px;height:5px;background:#7f9cf5;top:55%;left:15%;animation-duration:6s;animation-delay:2s}
.p-4{width:3px;height:3px;background:#63b3ed;bottom:25%;right:25%;animation-duration:8s;animation-delay:0.5s}
.p-5{width:4px;height:4px;background:#4fd1c5;bottom:15%;left:35%;animation-duration:5.5s;animation-delay:1.5s}
.p-6{width:3px;height:3px;background:#7f9cf5;top:75%;right:10%;animation-duration:6.5s;animation-delay:3s}

/* Logo */
.logo-glow {
  filter: drop-shadow(0 0 16px rgba(99,179,237,0.5));
  transition: filter 0.3s;
}
.logo-glow:hover { filter: drop-shadow(0 0 28px rgba(99,179,237,0.8)); }

/* Glass card */
.main-glass-card {
  background: rgba(255,255,255,0.04);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(99,179,237,0.15);
  border-radius: 28px;
  padding: 40px 44px;
  text-align: center;
  position: relative;
  width: 100%; max-width: 380px;
  box-shadow: 0 30px 60px rgba(0,0,0,0.4), inset 0 1px 0 rgba(99,179,237,0.1);
}
.glass-shine {
  position: absolute; top: 0; left: 15%; right: 15%; height: 1px;
  background: linear-gradient(90deg, transparent, rgba(99,179,237,0.4), transparent);
}

/* Icons */
.icon-row { display: flex; gap: 14px; justify-content: center; }
.glass-icon {
  width: 52px; height: 52px; border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid rgba(99,179,237,0.15);
  position: relative; overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
}
.glass-icon::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0;
  height: 50%; background: rgba(255,255,255,0.04);
}
.glass-icon:hover { transform: translateY(-4px); }
.gi-blue { background: rgba(49,130,206,0.12); box-shadow: 0 8px 20px rgba(49,130,206,0.2); }
.gi-blue:hover { box-shadow: 0 12px 28px rgba(49,130,206,0.4); }
.gi-teal { background: rgba(79,209,197,0.12); box-shadow: 0 8px 20px rgba(79,209,197,0.2); }
.gi-teal:hover { box-shadow: 0 12px 28px rgba(79,209,197,0.4); }
.gi-indigo { background: rgba(127,156,245,0.12); box-shadow: 0 8px 20px rgba(127,156,245,0.2); }
.gi-indigo:hover { box-shadow: 0 12px 28px rgba(127,156,245,0.4); }

.glass-title {
  font-size: 1.7rem; font-weight: 800;
  background: linear-gradient(135deg, #e2e8f0 30%, #63b3ed);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  letter-spacing: -0.4px;
}
.glass-desc { color: #4a6080; font-size: 13.5px; line-height: 1.7; }

/* Stats */
.stats-row {
  display: flex; align-items: center; justify-content: center;
  background: rgba(49,130,206,0.06);
  border: 1px solid rgba(99,179,237,0.1);
  border-radius: 16px; padding: 16px 0;
}
.stat-item { flex: 1; text-align: center; }
.stat-value { font-size: 16px; font-weight: 800; line-height: 1.2; }
.stat-label { font-size: 10.5px; color: #4a5568; font-weight: 500; margin-top: 3px; }
.stat-divider { width: 1px; height: 32px; background: rgba(99,179,237,0.1); }

/* Feature pills */
.feature-row { display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 300px; }
.feature-pill {
  display: flex; align-items: center; gap: 10px;
  background: rgba(49,130,206,0.06);
  border: 1px solid rgba(99,179,237,0.1);
  border-radius: 12px; padding: 10px 18px;
  font-size: 13px; color: #718096; font-weight: 500;
  transition: all 0.2s;
}
.feature-pill:hover {
  background: rgba(99,179,237,0.1); border-color: rgba(99,179,237,0.2); color: #a0aec0;
}
.pill-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
</style>