<template>
  <div class="profile-container">
    <div class="floating-elements">
      <div class="floating-circle circle-1"></div>
      <div class="floating-circle circle-2"></div>
      <div class="floating-circle circle-3"></div>
      <div class="floating-triangle triangle-1"></div>
      <div class="floating-triangle triangle-2"></div>
    </div>

    <!-- ===== LIGHTBOX OVERLAY ===== -->
    <Teleport to="body">
      <Transition name="lightbox">
        <div v-if="lightboxOpen" class="lightbox-overlay" @click="closeLightbox">
          <div class="lightbox-backdrop"></div>
          <div class="lightbox-content" @click.stop>
            <button class="lightbox-close" @click="closeLightbox">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
              </svg>
            </button>
            <div class="lightbox-img-wrapper">
              <img v-if="avatarPreview || authStore.user.avatar"
                :src="avatarPreview || avatarUrl"
                class="lightbox-img"
                alt="Profile Photo" />
              <div v-else class="lightbox-placeholder">
                {{ authStore.user.name ? authStore.user.name.charAt(0).toUpperCase() : 'U' }}
              </div>
            </div>
            <div class="lightbox-info">
              <div class="lightbox-name">{{ authStore.user.name || '—' }}</div>
              <div class="lightbox-role">User</div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
    <!-- ===== END LIGHTBOX ===== -->

    <div class="row g-5 g-xl-10">

      <!--begin::Sidebar-->
      <div class="col-xl-4">
        <div class="card card-dark animate-card-stagger-1">
          <div class="card-body position-relative">
            <div class="card-shine"></div>

            <div class="d-flex flex-center flex-column py-5">
              <div class="position-relative mb-7">
                <div class="symbol symbol-100px symbol-circle avatar-container animate-avatar-bounce">
                  <div class="avatar-glow-effect"></div>
                  <img v-if="avatarPreview || authStore.user.avatar"
                    :src="avatarPreview || avatarUrl" alt="avatar"
                    class="object-fit-cover rounded-circle avatar-image avatar-clickable"
                    @click="openLightbox"
                    title="Klik untuk lihat foto" />
                  <div v-else
                    class="symbol-label fs-1 fw-bold avatar-placeholder avatar-clickable"
                    @click="openLightbox"
                    title="Klik untuk lihat foto">
                    {{ authStore.user.name ? authStore.user.name.charAt(0).toUpperCase() : 'U' }}
                  </div>
                  <div class="avatar-view-hint" @click="openLightbox">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                      <circle cx="11" cy="11" r="8"></circle>
                      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                      <line x1="11" y1="8" x2="11" y2="14"></line>
                      <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                  </div>
                  <div class="avatar-pulse"></div>
                </div>
                <label for="avatar-input"
                  class="btn btn-icon btn-circle btn-active-color-primary w-30px h-30px bg-dark shadow position-absolute bottom-0 end-0 cursor-pointer btn-avatar-edit animate-btn-entrance"
                  title="Ganti foto profil">
                  <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span class="path2"></span></i>
                </label>
                <input id="avatar-input" type="file" accept="image/jpg,image/jpeg,image/png,image/webp"
                  class="d-none" @change="onAvatarChange" />
              </div>

              <div v-if="avatarPreview" class="d-flex gap-2 mb-4 animate-fade-in-up">
                <button type="button" class="btn btn-sm btn-primary fw-semibold" :disabled="avatarUploading" @click="onAvatarUpload">
                  <span v-if="!avatarUploading"><i class="ki-duotone ki-check fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>Simpan Foto</span>
                  <span v-else>Mengupload... <span class="spinner-border spinner-border-sm ms-2"></span></span>
                </button>
                <button type="button" class="btn btn-sm btn-light-danger fw-semibold" @click="onAvatarCancel">Batal</button>
              </div>

              <span class="fs-3 text-gray-800 fw-bold mb-1 animate-text-reveal">{{ authStore.user.name || '—' }}</span>
              <div class="fs-5 fw-semibold text-muted mb-3">User</div>
              <div class="d-flex gap-2 flex-wrap justify-content-center">
                <span class="badge badge-light fw-bold fs-8 px-3 py-2 text-capitalize animate-bounce-in">
                  <i class="ki-duotone ki-shield-tick fs-7 text-primary me-1"><span class="path1"></span><span class="path2"></span></i>
                  {{ authStore.user.role || 'user' }}
                </span>
                <span class="badge badge-light-success fw-bold fs-8 px-3 py-2 animate-bounce-in-delay">
                  <span class="bullet bullet-dot bg-success me-1 h-6px w-6px"></span>
                  Akun Aktif
                </span>
              </div>
            </div>

            <div class="separator separator-glass my-5"></div>

            <div class="pb-5 pt-5 px-2">
              <div class="fw-bold fs-7 text-muted text-uppercase mb-4 ps-1">Informasi Kontak</div>
              <div class="d-flex align-items-center mb-4 contact-item animate-contact-1">
                <span class="symbol symbol-35px me-4"><span class="symbol-label bg-light-primary"><i class="ki-duotone ki-sms fs-5 text-primary"><span class="path1"></span><span class="path2"></span></i></span></span>
                <div class="d-flex flex-column"><span class="text-muted fs-8 fw-semibold">Email</span><span class="text-gray-800 fs-7 fw-bold">{{ authStore.user.email || '—' }}</span></div>
              </div>
              <div class="d-flex align-items-center contact-item animate-contact-2">
                <span class="symbol symbol-35px me-4"><span class="symbol-label bg-light-success"><i class="ki-duotone ki-phone fs-5 text-success"><span class="path1"></span><span class="path2"></span></i></span></span>
                <div class="d-flex flex-column"><span class="text-muted fs-8 fw-semibold">Telepon</span><span class="text-gray-800 fs-7 fw-bold">{{ authStore.user.phone || '—' }}</span></div>
              </div>
            </div>

            <div class="separator separator-glass mb-5"></div>

            <div v-if="authStore.user.bio" class="px-2 pb-5">
              <div class="fw-bold fs-7 text-muted text-uppercase mb-3 ps-1">Bio</div>
              <p class="text-gray-700 fs-7 fw-semibold mb-0 lh-base bio-text animate-bio-appear">{{ authStore.user.bio }}</p>
            </div>
            <div v-else class="px-2 pb-5">
              <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 animate-notice-appear">
                <i class="ki-duotone ki-information-5 fs-2x text-warning me-3 flex-shrink-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="fs-8 text-muted fw-semibold">Bio belum diisi. Tambahkan bio untuk melengkapi profil kamu.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card card-dark animate-card-stagger-2">
          <div class="card-header border-0 pt-5 position-relative">
            <div class="card-shine"></div>
            <div class="card-title flex-column">
              <h3 class="fw-bold mb-1 fs-6">Kelengkapan Profil</h3>
              <div class="fs-8 text-muted fw-semibold">{{ filledCount }} dari {{ totalFields }} field terisi</div>
            </div>
            <div class="card-toolbar">
              <span class="badge badge-dark fw-bold fs-7">{{ profileCompletion }}%</span>
            </div>
          </div>
          <div class="card-body pt-2 pb-6">
            <div class="progress progress-dark h-8px mb-5 rounded-pill">
              <div class="progress-bar bg-primary rounded-pill animate-progress-fill" :style="{ width: profileCompletion + '%' }" role="progressbar"></div>
            </div>
            <div class="d-flex flex-column gap-3">
              <div v-for="(field, index) in fieldStatus" :key="field.key"
                class="d-flex align-items-center justify-content-between field-item animate-field-stagger"
                :style="{ animationDelay: (index * 0.1) + 's' }">
                <div class="d-flex align-items-center">
                  <span :class="['bullet bullet-dot me-3 h-7px w-7px', field.filled ? 'bg-success' : 'bg-danger']"></span>
                  <span class="text-gray-700 fw-semibold fs-8">{{ field.label }}</span>
                </div>
                <span v-if="field.filled" class="badge badge-light-success fw-bold fs-9">✓ Terisi</span>
                <span v-else class="badge badge-light-danger fw-bold fs-9">Kosong</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--end::Sidebar-->

      <!--begin::Main Form-->
      <div class="col-xl-8">
        <div class="card card-dark animate-card-stagger-3">
          <div class="card-header border-0 pt-6 position-relative">
            <div class="card-shine"></div>
            <div class="card-title flex-column">
              <h3 class="fw-bold mb-1 fs-3">Edit Profil</h3>
              <div class="fs-7 text-muted fw-semibold">Perbarui informasi akun kamu</div>
            </div>
          </div>
          <div class="card-body pt-0">
            <VForm @submit="onSubmitProfile" :validation-schema="profileSchema" :initial-values="initialValues">

              <div class="separator separator-dark mb-8">
                <span class="separator-content text-muted fw-bold fs-8 text-uppercase px-4 bg-dark">Data Pribadi</span>
              </div>

              <div class="row mb-6 form-row animate-form-row-1">
                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama Lengkap</label>
                <div class="col-lg-8 fv-row">
                  <div class="position-relative input-container">
                    <span class="position-absolute top-50 translate-middle-y ms-4 icon-input">
                      <i class="ki-duotone ki-profile-circle fs-4 text-muted"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    </span>
                    <Field type="text" name="name" class="form-control form-control-lg form-control-dark ps-12" placeholder="Masukkan nama lengkap" />
                  </div>
                  <div class="fv-plugins-message-container invalid-feedback"><ErrorMessage name="name" /></div>
                </div>
              </div>

              <div class="row mb-6 form-row animate-form-row-2">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">No. Telepon</label>
                <div class="col-lg-8 fv-row">
                  <div class="position-relative input-container">
                    <span class="position-absolute top-50 translate-middle-y ms-4 icon-input">
                      <i class="ki-duotone ki-phone fs-4 text-muted"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                    <Field type="text" name="phone" class="form-control form-control-lg form-control-dark ps-12" placeholder="Contoh: +62812xxxxxxxx" />
                  </div>
                </div>
              </div>

              <div class="separator separator-dark mb-8 mt-2">
                <span class="separator-content text-muted fw-bold fs-8 text-uppercase px-4 bg-dark">Tentang Kamu</span>
              </div>

              <div class="row mb-8 form-row animate-form-row-3">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">Bio <span class="ms-1 fs-8 text-muted fw-normal d-block">Opsional</span></label>
                <div class="col-lg-8 fv-row">
                  <Field as="textarea" name="bio" class="form-control form-control-lg form-control-dark" rows="5" placeholder="Ceritakan sedikit tentang kamu..." />
                  <div class="text-muted fs-8 mt-2">Maksimal 300 karakter</div>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center pt-5 border-top form-footer animate-form-footer">
                <div class="text-muted fw-semibold fs-8 info-message">
                  <i class="ki-duotone ki-information-5 fs-7 me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                  Perubahan akan langsung diterapkan setelah disimpan.
                </div>
                <div class="d-flex gap-3">
                  <button type="reset" class="btn btn-black-outline fw-semibold">Reset</button>
                  <button type="submit" ref="submitButton" class="btn btn-black-solid fw-semibold">
                    <span class="indicator-label">
                      <i class="ki-duotone ki-check fs-4 me-1"><span class="path1"></span><span class="path2"></span></i>
                      Simpan Perubahan
                    </span>
                    <span class="indicator-progress">
                      Menyimpan...
                      <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                  </button>
                </div>
              </div>

            </VForm>
          </div>
        </div>
      </div>
      <!--end::Main Form-->

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed, ref, onMounted, onBeforeUnmount } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import { useAuthStore } from "@/stores/auth";
import Swal from "sweetalert2/dist/sweetalert2.js";
import * as Yup from "yup";

export default defineComponent({
  name: "user-profile",
  components: { Field, VForm, ErrorMessage },
  setup() {
    const authStore = useAuthStore();
    const submitButton = ref<HTMLButtonElement | null>(null);

    const avatarPreview = ref<string | null>(null);
    const avatarFile = ref<File | null>(null);
    const avatarUploading = ref(false);

    const lightboxOpen = ref(false);
    const openLightbox = () => { lightboxOpen.value = true; document.body.style.overflow = 'hidden'; };
    const closeLightbox = () => { lightboxOpen.value = false; document.body.style.overflow = ''; };
    const onKeyDown = (e: KeyboardEvent) => { if (e.key === 'Escape') closeLightbox(); };

    const avatarUrl = computed(() => {
      if (!authStore.user.avatar) return null;
      return `${import.meta.env.VITE_APP_API_URL?.replace('/api', '')}/storage/${authStore.user.avatar}`;
    });

    const onAvatarChange = (event: Event) => {
      const input = event.target as HTMLInputElement;
      if (!input.files || !input.files[0]) return;
      const file = input.files[0];
      if (file.size > 2 * 1024 * 1024) {
        Swal.fire({ text: "Ukuran foto maksimal 2MB!", icon: "error", buttonsStyling: false, confirmButtonText: "Ok", heightAuto: false, customClass: { confirmButton: "btn btn-light-danger" } });
        return;
      }
      avatarFile.value = file;
      avatarPreview.value = URL.createObjectURL(file);
    };

    const onAvatarUpload = async () => {
      if (!avatarFile.value) return;
      avatarUploading.value = true;
      await authStore.uploadAvatar(avatarFile.value);
      const error = Object.values(authStore.errors);
      if (error.length === 0) {
        Swal.fire({ text: "Foto profil berhasil diperbarui!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok!", heightAuto: false, customClass: { confirmButton: "btn btn-light-primary" } });
        avatarPreview.value = null;
        avatarFile.value = null;
      } else {
        Swal.fire({ text: error[0] as string, icon: "error", buttonsStyling: false, confirmButtonText: "Coba lagi!", heightAuto: false, customClass: { confirmButton: "btn btn-light-danger" } });
      }
      avatarUploading.value = false;
    };

    const onAvatarCancel = () => {
      avatarPreview.value = null;
      avatarFile.value = null;
      const input = document.getElementById('avatar-input') as HTMLInputElement;
      if (input) input.value = '';
    };

    // Field config tanpa jabatan & perusahaan
    const fieldConfig = [
      { key: "name",  label: "Nama Lengkap" },
      { key: "email", label: "Email" },
      { key: "phone", label: "No. Telepon" },
      { key: "bio",   label: "Bio" },
    ];

    const fieldStatus = computed(() =>
      fieldConfig.map(f => ({ key: f.key, label: f.label, filled: !!authStore.user[f.key as keyof typeof authStore.user] }))
    );
    const filledCount = computed(() => fieldStatus.value.filter(f => f.filled).length);
    const totalFields = fieldConfig.length;
    const profileCompletion = computed(() => Math.round((filledCount.value / totalFields) * 100));

    const profileSchema = Yup.object().shape({
      name:  Yup.string().required("Nama wajib diisi").label("Nama"),
      phone: Yup.string().nullable().label("Telepon"),
      bio:   Yup.string().max(300, "Bio maksimal 300 karakter").nullable().label("Bio"),
    });

    const initialValues = computed(() => ({
      name:  authStore.user.name  || "",
      phone: authStore.user.phone || "",
      bio:   authStore.user.bio   || "",
    }));

    const onSubmitProfile = async (values: any) => {
      if (submitButton.value) {
        submitButton.value.disabled = true;
        submitButton.value.setAttribute("data-kt-indicator", "on");
      }
      await authStore.updateProfile(values);
      const error = Object.values(authStore.errors);
      if (error.length === 0) {
        Swal.fire({ text: "Profil berhasil diperbarui!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok, Lanjutkan!", heightAuto: false, customClass: { confirmButton: "btn fw-semibold btn-light-primary" } });
      } else {
        Swal.fire({ text: error[0] as string, icon: "error", buttonsStyling: false, confirmButtonText: "Coba Lagi", heightAuto: false, customClass: { confirmButton: "btn fw-semibold btn-light-danger" } });
      }
      submitButton.value?.removeAttribute("data-kt-indicator");
      submitButton.value!.disabled = false;
    };

    // Proximity effect
    let rafId: number | null = null;
    let lastMouse = { x: -9999, y: -9999 };
    const proximitySelectors = ['.card-dark', '.avatar-container', '.contact-item', '.field-item', '.btn-black-solid', '.btn-black-outline'];
    let proximityElements: HTMLElement[] = [];

    const getMaxScale = (el: HTMLElement) => {
      if (el.classList.contains('avatar-container')) return 1.12;
      if (el.classList.contains('card-dark')) return 1.06;
      if (el.classList.contains('contact-item')) return 1.06;
      if (el.classList.contains('field-item')) return 1.04;
      if (el.classList.contains('btn-black-solid') || el.classList.contains('btn-black-outline')) return 1.08;
      return 1.04;
    };

    const updateProximity = () => {
      for (const el of proximityElements) {
        const rect = el.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        const dx = lastMouse.x - cx;
        const dy = lastMouse.y - cy;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const threshold = Math.max(rect.width, rect.height) * 1.6;
        let scale = 1;
        if (dist < threshold) {
          const maxScale = getMaxScale(el);
          const t = 1 - dist / threshold;
          scale = 1 + (maxScale - 1) * Math.pow(t, 1.2);
        }
        if (!el.dataset.origTransform) {
          const computed = getComputedStyle(el).transform || 'none';
          el.dataset.origTransform = computed;
        }
        const orig = el.dataset.origTransform || 'none';
        el.style.transition = 'transform 120ms ease-out';
        el.style.transform = (orig && orig !== 'none') ? `${orig} scale(${scale})` : `scale(${scale})`;
      }
      rafId = null;
    };

    const onMouseMove = (e: MouseEvent) => {
      lastMouse.x = e.clientX;
      lastMouse.y = e.clientY;
      if (rafId == null) rafId = requestAnimationFrame(updateProximity);
    };

    const onMouseLeave = () => {
      for (const el of proximityElements) {
        el.style.transition = 'transform 180ms ease';
        const orig = el.dataset.origTransform || 'none';
        el.style.transform = (orig && orig !== 'none') ? orig : '';
      }
    };

    onMounted(() => {
      const container = document.querySelector('.profile-container') as HTMLElement | null;
      if (container) {
        proximityElements = Array.from(container.querySelectorAll(proximitySelectors.join(','))) as HTMLElement[];
      }
      window.addEventListener('mousemove', onMouseMove, { passive: true });
      window.addEventListener('mouseleave', onMouseLeave, { passive: true });
      window.addEventListener('keydown', onKeyDown);
    });

    onBeforeUnmount(() => {
      if (rafId) cancelAnimationFrame(rafId);
      window.removeEventListener('mousemove', onMouseMove);
      window.removeEventListener('mouseleave', onMouseLeave);
      window.removeEventListener('keydown', onKeyDown);
      document.body.style.overflow = '';
    });

    return {
      authStore, submitButton, profileSchema, initialValues, onSubmitProfile,
      fieldStatus, filledCount, totalFields, profileCompletion,
      avatarPreview, avatarUploading, avatarUrl,
      onAvatarChange, onAvatarUpload, onAvatarCancel,
      lightboxOpen, openLightbox, closeLightbox,
    };
  },
});
</script>

<style scoped>
.profile-container { position: relative; overflow: hidden; min-height: 100vh; }
.floating-elements { position: absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:0; }
.floating-circle { position:absolute; border-radius:50%; background:rgba(255,255,255,0.02); }
.circle-1 { width:80px; height:80px; top:10%; left:10%; }
.circle-2 { width:120px; height:120px; top:60%; right:15%; }
.circle-3 { width:60px; height:60px; bottom:20%; left:20%; }
.floating-triangle { position:absolute; }
.triangle-1 { border-left:40px solid transparent; border-right:40px solid transparent; border-bottom:70px solid rgba(255,255,255,0.03); top:20%; right:10%; }
.triangle-2 { border-left:30px solid transparent; border-right:30px solid transparent; border-bottom:50px solid rgba(255,255,255,0.03); bottom:30%; right:25%; }

.lightbox-overlay { position: fixed; inset: 0; z-index: 99999; display: flex; align-items: center; justify-content: center; }
.lightbox-backdrop { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
.lightbox-content { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 20px; }
.lightbox-close { position: absolute; top: -50px; right: -10px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.lightbox-close:hover { background: rgba(255,255,255,0.2); transform: scale(1.1); }
.lightbox-img-wrapper { border-radius: 50%; overflow: hidden; width: 280px; height: 280px; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 0 0 8px rgba(255,255,255,0.05), 0 30px 80px rgba(0,0,0,0.6); }
.lightbox-img { width: 100%; height: 100%; object-fit: cover; }
.lightbox-placeholder { width: 100%; height: 100%; background: #1a1a1a; display: flex; align-items: center; justify-content: center; font-size: 80px; font-weight: 800; color: white; }
.lightbox-info { text-align: center; }
.lightbox-name { font-size: 20px; font-weight: 700; color: white; margin-bottom: 4px; }
.lightbox-role { font-size: 14px; color: rgba(255,255,255,0.5); }
.lightbox-enter-active { transition: opacity 0.25s ease; }
.lightbox-leave-active { transition: opacity 0.2s ease; }
.lightbox-enter-from, .lightbox-leave-to { opacity: 0; }
.lightbox-enter-active .lightbox-content { transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1); }
.lightbox-leave-active .lightbox-content { transition: transform 0.2s ease; }
.lightbox-enter-from .lightbox-content { transform: scale(0.5); }
.lightbox-leave-to .lightbox-content { transform: scale(0.7); }

.avatar-clickable { cursor: pointer; }
.avatar-container { position: relative; transition: all 0.4s; }
.avatar-view-hint { position: absolute; inset: 0; border-radius: 50%; background: rgba(0,0,0,0.45); display: flex; align-items: center; justify-content: center; opacity: 0; cursor: pointer; transition: opacity 0.25s ease; z-index: 2; }
.avatar-container:hover .avatar-view-hint { opacity: 1; }

.card-dark { background:#111; border:1px solid #222; border-radius:20px; box-shadow:0 8px 32px rgba(0,0,0,0.5); position:relative; overflow:hidden; transition:all 0.4s cubic-bezier(0.4,0,0.2,1); }
.card-dark:hover { transform:translateY(-6px); box-shadow:0 20px 60px rgba(0,0,0,0.6); background:#1a1a1a; }
.card-shine { position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.05),transparent); pointer-events:none; }

.animate-card-stagger-1 { animation:cardSlideIn 0.35s cubic-bezier(0.4,0,0.2,1) forwards; opacity:0; }
.animate-card-stagger-2 { animation:cardSlideIn 0.35s cubic-bezier(0.4,0,0.2,1) 0.06s forwards; opacity:0; }
.animate-card-stagger-3 { animation:cardSlideIn 0.35s cubic-bezier(0.4,0,0.2,1) 0.12s forwards; opacity:0; }

.avatar-glow-effect { position:absolute; top:-5px; left:-5px; right:-5px; bottom:-5px; border-radius:50%; background:linear-gradient(45deg,rgba(255,255,255,0.1),rgba(255,255,255,0.05)); opacity:0; transition:opacity 0.4s; z-index:-1; }
.avatar-container:hover .avatar-glow-effect { opacity:1; }
.avatar-image { border:3px solid rgba(255,255,255,0.1); transition:all 0.4s; }
.avatar-container:hover .avatar-image { border-color:rgba(255,255,255,0.2); }
.avatar-pulse { position:absolute; top:-10px; left:-10px; right:-10px; bottom:-10px; border-radius:50%; border:2px solid rgba(255,255,255,0.1); opacity:0.6; }
.avatar-placeholder { background:#1a1a1a !important; color:#fff !important; border:3px solid rgba(255,255,255,0.1); }
.animate-avatar-bounce { animation:avatarBounce 0.36s cubic-bezier(0.68,-0.55,0.265,1.55) 0.12s both; }

.badge-dark { background:#1a1a1a; border:1px solid #444; color:#fff; }
.animate-text-reveal { animation:textReveal 0.36s cubic-bezier(0.4,0,0.2,1) 0.18s both; opacity:0; }

.contact-item { padding:12px; border-radius:12px; transition:all 0.3s; background:#1a1a1a; border:1px solid #333; }
.contact-item:hover { transform:translateX(8px); background:#222; box-shadow:0 8px 25px rgba(0,0,0,0.4); }
.animate-contact-1 { animation:contactSlideIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.30s both; opacity:0; }
.animate-contact-2 { animation:contactSlideIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.36s both; opacity:0; }

.bio-text { color:#e0e0e0; transition:all 0.3s; }
.bio-text:hover { color:#fff; }
.animate-bio-appear { animation:bioFadeIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.50s both; opacity:0; }
.animate-notice-appear { animation:noticeSlideUp 0.36s cubic-bezier(0.4,0,0.2,1) 0.52s both; opacity:0; }

.progress-dark { background:#1a1a1a; border-radius:20px; overflow:hidden; }
.animate-progress-fill { animation:progressFill 0.6s cubic-bezier(0.4,0,0.2,1) 0.60s both; }

.field-item { padding:10px; border-radius:10px; transition:all 0.3s; background:#1a1a1a; border:1px solid #333; }
.field-item:hover { transform:translateX(5px); background:#222; box-shadow:0 5px 20px rgba(0,0,0,0.4); }
.animate-field-stagger { animation:fieldSlideIn 0.35s cubic-bezier(0.4,0,0.2,1) forwards; opacity:0; }

.form-control-dark { background:#1a1a1a; border:1px solid #444; border-radius:12px; color:#fff; transition:all 0.3s; }
.form-control-dark:focus { background:#222; border-color:#667eea; box-shadow:0 0 20px rgba(102,126,234,0.2); color:#fff; }
.form-control-dark::placeholder { color:#888; }
.input-container { position:relative; }
.icon-input { transition:all 0.3s; z-index:1; }
.input-container:hover .icon-input { transform:scale(1.1); }

.animate-form-row-1 { animation:formRowSlideIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.60s both; opacity:0; }
.animate-form-row-2 { animation:formRowSlideIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.66s both; opacity:0; }
.animate-form-row-3 { animation:formRowSlideIn 0.36s cubic-bezier(0.4,0,0.2,1) 0.72s both; opacity:0; }

.separator-dark { position:relative; border:none; height:1px; background:#333; margin:2rem 0; display:flex; align-items:center; justify-content:center; }
.bg-dark { background:#1a1a1a !important; color:#aaa; padding:0 0.75rem; font-size:0.7rem; }

.btn-black-outline { background:transparent; border:1px solid #555; color:#ccc; border-radius:12px; transition:all 0.2s ease; }
.btn-black-outline:hover { background:#222; color:#fff; border-color:#888; transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,0,0,0.5); }

.btn-black-solid { background:#000; border:1px solid #333; color:#fff; border-radius:12px; transition:all 0.2s ease; position:relative; }
.btn-black-solid:hover { background:#1a1a1a; color:#fff; transform:translateY(-2px); box-shadow:0 12px 30px rgba(0,0,0,0.6); }
.btn-black-solid .indicator-label { display:inline-flex !important; align-items:center; color:#fff; }
.btn-black-solid .indicator-progress { display:none !important; }
.btn-black-solid[data-kt-indicator="on"] .indicator-label { display:none !important; }
.btn-black-solid[data-kt-indicator="on"] .indicator-progress { display:inline-flex !important; align-items:center; color:#fff !important; }
.btn-black-solid[data-kt-indicator="on"] .spinner-border { border-color:rgba(255,255,255,0.4); border-top-color:#fff; }

.form-footer { padding:1.5rem; border-radius:15px; background:#1a1a1a; border:1px solid #333; transition:all 0.3s; }
.form-footer:hover { background:#222; }
.info-message { color:#ccc; }
.animate-form-footer { animation:footerSlideUp 0.36s cubic-bezier(0.4,0,0.2,1) 0.90s both; opacity:0; }
.animate-btn-entrance { animation:btnEntrance 0.36s cubic-bezier(0.4,0,0.2,1) 0.18s both; opacity:0; }

@keyframes cardSlideIn { 0%{opacity:0;transform:translateY(40px) scale(0.95)} 100%{opacity:1;transform:translateY(0) scale(1)} }
@keyframes avatarBounce { 0%{transform:scale(0.3);opacity:0} 50%{transform:scale(1.05)} 70%{transform:scale(0.9)} 100%{transform:scale(1);opacity:1} }
@keyframes contactSlideIn { 0%{opacity:0;transform:translateX(-20px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes bioFadeIn { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes noticeSlideUp { 0%{opacity:0;transform:translateY(18px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes progressFill { 0%{width:0%} 100%{width:inherit} }
@keyframes fieldSlideIn { 0%{opacity:0;transform:translateX(-12px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes formRowSlideIn { 0%{opacity:0;transform:translateY(18px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes btnEntrance { 0%{opacity:0;transform:scale(0.9) translateY(10px)} 100%{opacity:1;transform:scale(1) translateY(0)} }
@keyframes footerSlideUp { 0%{opacity:0;transform:translateY(18px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes textReveal { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }

@media (max-width: 768px) {
  .floating-circle, .floating-triangle { display:none; }
  .contact-item:hover, .field-item:hover { transform:none; }
  .lightbox-img-wrapper { width: 220px; height: 220px; }
}
</style>