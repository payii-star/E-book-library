<template>
  <div class="w-lg-500px p-10">
    <div class="sign-up-container">
      <VForm class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework" novalidate @submit="onSubmitRegister" id="kt_login_signup_form" :validation-schema="registration">

        <div class="mb-10">
          <div class="greeting-badge mb-4">
            <span class="dot"></span>
            <span>Bergabung sekarang</span>
          </div>
          <h1 class="sign-up-title">Buat Akun Baru</h1>
          <div class="sign-up-subtitle">
            Sudah punya akun?
            <router-link to="/sign-in" class="accent-link">Masuk di sini →</router-link>
          </div>
        </div>

        <button type="button" class="google-btn w-100 mb-7">
          <img :src="getAssetPath('media/svg/brand-logos/google-icon.svg')" class="h-18px me-2" alt="Google"/>
          Daftar dengan Google
        </button>

        <div class="divider-wrapper mb-7">
          <span class="divider-line"></span>
          <span class="divider-text">atau isi form</span>
          <span class="divider-line"></span>
        </div>

        <!-- Nama Lengkap -->
        <div class="fv-row mb-5">
          <label class="input-label">Nama Lengkap</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </span>
            <Field class="custom-input" type="text" placeholder="Nama lengkap kamu" name="name" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')"/>
          </div>
          <div class="fv-plugins-message-container">
            <div class="fv-help-block error-text"><ErrorMessage name="name"/></div>
          </div>
        </div>

        <!-- Email -->
        <div class="fv-row mb-5">
          <label class="input-label">Email</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </span>
            <Field class="custom-input" type="email" placeholder="nama@email.com" name="email" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')"/>
          </div>
          <div class="fv-plugins-message-container"><div class="fv-help-block error-text"><ErrorMessage name="email"/></div></div>
        </div>

        <!-- Password -->
        <div class="fv-row mb-5">
          <label class="input-label">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <Field class="custom-input" :type="showPassword ? 'text' : 'password'" placeholder="Min. 8 karakter" name="password" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')"/>
            <button type="button" class="eye-btn" @click="showPassword = !showPassword" tabindex="-1">
              <svg v-if="showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
          <div class="fv-plugins-message-container"><div class="fv-help-block error-text"><ErrorMessage name="password"/></div></div>
          <!-- Password strength bar -->
          <div class="strength-bar mt-3">
            <div v-for="i in 4" :key="i" class="strength-segment" :class="{ active: passwordStrength >= i }"></div>
          </div>
          <div class="password-hint mt-2">Gunakan 8+ karakter dengan kombinasi huruf, angka & simbol.</div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="fv-row mb-5">
          <label class="input-label">Konfirmasi Password</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </span>
            <Field class="custom-input" :type="showConfirm ? 'text' : 'password'" placeholder="Ulangi password" name="password_confirmation" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')"/>
            <button type="button" class="eye-btn" @click="showConfirm = !showConfirm" tabindex="-1">
              <svg v-if="showConfirm" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
          <div class="fv-plugins-message-container"><div class="fv-help-block error-text"><ErrorMessage name="password_confirmation"/></div></div>
        </div>

        <!-- Terms -->
        <div class="fv-row mb-7">
          <label class="toc-wrapper">
            <Field class="toc-checkbox" type="checkbox" name="toc" value="1"/>
            <span class="toc-text">Saya menyetujui <a href="#" class="accent-link">Syarat & Ketentuan</a> yang berlaku.</span>
          </label>
        </div>

        <!-- Error alert -->
        <div v-if="registerError" class="error-alert mb-5">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          {{ registerError }}
        </div>

        <!-- Popup sukses -->
        <Teleport to="body">
          <div v-if="showSuccess" class="success-overlay">
            <div class="success-popup">
              <div class="success-circle">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
              </div>
              <div class="success-title">Akun Berhasil Dibuat!</div>
              <div class="success-sub">Mengalihkan ke dashboard...</div>
            </div>
          </div>
        </Teleport>

        <!-- Submit -->
        <button type="submit" class="submit-btn w-100" :disabled="loading">
          <span v-if="!loading" class="indicator-label">
            <span>Buat Akun</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>
          <span v-else class="indicator-progress">
            <span class="spinner-border spinner-border-sm me-2"></span>Mohon tunggu...
          </span>
        </button>

      </VForm>
    </div>
  </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref, watch } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import { useAuthStore, type User } from "@/stores/auth";
import { useRouter } from "vue-router";

export default defineComponent({
  name: "sign-up",
  components: { Field, VForm, ErrorMessage },
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const loading = ref(false);
    const registerError = ref("");
    const showSuccess = ref(false);
    const showPassword = ref(false);
    const showConfirm = ref(false);
    const passwordStrength = ref(0);
    const passwordValue = ref("");

    const registration = Yup.object().shape({
      name: Yup.string().required().label("Nama Lengkap"),
      email: Yup.string().min(4).required().email().label("Email"),
      password: Yup.string().min(8).required().label("Password"),
      password_confirmation: Yup.string()
        .required()
        .oneOf([Yup.ref("password")], "Password tidak cocok")
        .label("Konfirmasi Password"),
    });

    const calcStrength = (val: string) => {
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;
      return score;
    };

    watch(passwordValue, (val) => {
      passwordStrength.value = calcStrength(val);
    });

    const onSubmitRegister = async (values: any) => {
      registerError.value = "";
      loading.value = true;
      store.errors = {};

      await store.register(values as User);

      loading.value = false;

      const error = Object.values(store.errors);
      if (error.length === 0) {
        showSuccess.value = true;
        setTimeout(() => {
          router.push({ name: "user-dashboard" });
        }, 300);
      } else {
        registerError.value = error[0] as string;
      }
    };

    return { registration, onSubmitRegister, loading, registerError, showSuccess, showPassword, showConfirm, passwordStrength, passwordValue, getAssetPath };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

.sign-up-container { font-family: 'DM Sans', sans-serif; }

.greeting-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(99,179,237,0.1); border: 1px solid rgba(99,179,237,0.25);
  color: #63b3ed; padding: 6px 14px; border-radius: 100px;
  font-size: 12px; font-weight: 500;
}
.greeting-badge .dot {
  width: 6px; height: 6px; background: #63b3ed; border-radius: 50%;
  animation: pulseDot 2s infinite;
}
@keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }

.sign-up-title {
  font-family: 'Syne', sans-serif; font-size: 2.2rem; font-weight: 800;
  color: #f0f4ff; margin: 12px 0 8px; line-height: 1.15; letter-spacing: -0.5px;
}
.sign-up-subtitle { color: #8896b3; font-size: 14px; }
.accent-link { color: #63b3ed; font-weight: 600; text-decoration: none; transition: color 0.2s; }
.accent-link:hover { color: #90cdf4; }

.google-btn {
  display: flex; align-items: center; justify-content: center;
  background: rgba(49,130,206,0.06); border: 1.5px solid rgba(99,179,237,0.15);
  border-radius: 12px; padding: 13px 20px; color: #a0aec0;
  font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif;
  cursor: pointer; transition: all 0.2s;
}
.google-btn:hover {
  background: rgba(99,179,237,0.1); border-color: rgba(99,179,237,0.28);
  color: #e2e8f0; transform: translateY(-1px);
}

.divider-wrapper { display: flex; align-items: center; gap: 12px; }
.divider-line { flex: 1; height: 1px; background: rgba(99,179,237,0.12); }
.divider-text { color: #2d4a6a; font-size: 12px; font-weight: 500; white-space: nowrap; }

.input-label {
  display: block; font-size: 12px; font-weight: 600; color: #a0aec0;
  margin-bottom: 8px; letter-spacing: 0.5px; text-transform: uppercase;
}
.input-wrapper { position: relative; }
.input-icon {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  color: #2b6cb0; display: flex; align-items: center; pointer-events: none; z-index: 1;
}
.custom-input {
  width: 100%; background: rgba(49,130,206,0.06);
  border: 1.5px solid rgba(99,179,237,0.18);
  border-radius: 12px; padding: 13px 44px;
  color: #e2e8f0; font-size: 14px; font-family: 'DM Sans', sans-serif;
  transition: all 0.25s; outline: none;
}
.custom-input::placeholder { color: #2d4a6a; }
.custom-input:focus {
  border-color: #63b3ed; background: rgba(99,179,237,0.08);
  box-shadow: 0 0 0 4px rgba(99,179,237,0.1);
}
.error-text { color: #fc8181; font-size: 12px; margin-top: 5px; }
.password-hint { color: #2d4a6a; font-size: 11.5px; }

/* Eye toggle */
.eye-btn {
  position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: #4a6080; cursor: pointer;
  display: flex; align-items: center; padding: 0; transition: color 0.2s; z-index: 1;
}
.eye-btn:hover { color: #63b3ed; }

/* Strength bar */
.strength-bar { display: flex; gap: 6px; }
.strength-segment {
  flex: 1; height: 4px; border-radius: 4px;
  background: rgba(99,179,237,0.12); transition: background 0.3s;
}
.strength-segment.active { background: #63b3ed; }

.toc-wrapper { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; }
.toc-checkbox { width: 16px; height: 16px; margin-top: 2px; accent-color: #63b3ed; flex-shrink: 0; }
.toc-text { color: #8896b3; font-size: 13px; line-height: 1.5; }

/* Error alert */
.error-alert {
  display: flex; align-items: center; gap: 8px;
  background: rgba(229,62,62,0.1); border: 1px solid rgba(229,62,62,0.25);
  border-radius: 10px; padding: 12px 14px;
  color: #fc8181; font-size: 13px; font-weight: 500;
}

.submit-btn {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  background: linear-gradient(135deg, #2b6cb0 0%, #1C325E 100%);
  color: #fff; border: none; border-radius: 12px; padding: 15px 24px;
  font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif;
  cursor: pointer; transition: all 0.25s; position: relative; overflow: hidden; width: 100%;
}
.submit-btn::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
  pointer-events: none;
}
.submit-btn:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(43,108,176,0.5); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.submit-btn .indicator-label { display: flex; align-items: center; gap: 8px; }
.indicator-progress { display: flex; align-items: center; justify-content: center; gap: 8px; }
.spinner-border {
  display: inline-block; width: 16px; height: 16px;
  border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
  border-radius: 50%; animation: spin 0.7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Popup sukses */
.success-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6);
  backdrop-filter: blur(8px); display: flex; align-items: center;
  justify-content: center; z-index: 99999; animation: fadeIn 0.2s ease;
}
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
.success-popup {
  background: #0a0f1e; border: 1px solid rgba(99,179,237,0.3);
  border-radius: 24px; padding: 40px 48px; text-align: center;
  box-shadow: 0 0 40px rgba(99,179,237,0.15);
  animation: popUp 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes popUp { from{opacity:0;transform:scale(0.8)} to{opacity:1;transform:scale(1)} }
.success-circle {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(99,179,237,0.15); border: 2px solid rgba(99,179,237,0.4);
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px; color: #63b3ed;
  animation: scaleIn 0.4s cubic-bezier(0.34,1.56,0.64,1) 0.1s both;
}
@keyframes scaleIn { from{transform:scale(0)} to{transform:scale(1)} }
.success-title { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: #f0f4ff; margin-bottom: 6px; }
.success-sub { font-size: 13px; color: #63b3ed; }
</style>