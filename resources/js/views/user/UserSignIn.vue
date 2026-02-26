<template>
  <div class="w-lg-500px p-10">
    <div class="sign-in-container">
      <VForm class="form w-100" id="kt_login_signin_form" @submit="onSubmitLogin" :validation-schema="login">

        <div class="mb-10">
          <div class="greeting-badge mb-4">
            <span class="dot"></span>
            <span>Selamat datang kembali</span>
          </div>
          <h1 class="sign-in-title">Masuk ke Akun</h1>
          <div class="sign-in-subtitle">
            Belum punya akun?
            <router-link to="/sign-up" class="accent-link">Daftar sekarang →</router-link>
          </div>
        </div>

        <div class="fv-row mb-6">
          <label class="input-label">Email</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </span>
            <Field tabindex="1" class="custom-input" type="text" name="email" placeholder="nama@email.com" autocomplete="off"/>
          </div>
          <div class="fv-plugins-message-container">
            <div class="fv-help-block error-text"><ErrorMessage name="email"/></div>
          </div>
        </div>

        <div class="fv-row mb-8">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="input-label mb-0">Password</label>
            <router-link to="/password-reset" class="accent-link" style="font-size:12.5px">Lupa password?</router-link>
          </div>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <Field tabindex="2" class="custom-input" type="password" name="password" placeholder="••••••••" autocomplete="off"/>
          </div>
          <div class="fv-plugins-message-container">
            <div class="fv-help-block error-text"><ErrorMessage name="password"/></div>
          </div>
        </div>

        <div>
          <button tabindex="3" type="submit" ref="submitButton" id="kt_sign_in_submit" class="submit-btn w-100 mb-6">
            <span class="indicator-label">
              <span>Masuk</span>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </span>
            <span class="indicator-progress">
              <span class="spinner-border spinner-border-sm me-2"></span>Mohon tunggu...
            </span>
          </button>

          <div class="divider-wrapper mb-6">
            <span class="divider-line"></span>
            <span class="divider-text">atau lanjutkan dengan</span>
            <span class="divider-line"></span>
          </div>

          <div class="social-buttons">
            <a href="#" class="social-btn">
              <img :src="getAssetPath('media/svg/brand-logos/google-icon.svg')" class="h-15px me-2" alt="Google"/>Google
            </a>
            <a href="#" class="social-btn">
              <img :src="getAssetPath('media/svg/brand-logos/facebook-4.svg')" class="h-15px me-2" alt="Facebook"/>Facebook
            </a>
            <a href="#" class="social-btn social-btn-full">
              <img :src="getAssetPath('media/svg/brand-logos/apple-black.svg')" class="h-15px me-2" alt="Apple"/>Apple
            </a>
          </div>
        </div>

      </VForm>
    </div>
  </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, ref } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import { useAuthStore, type User } from "@/stores/auth";
import { useRouter } from "vue-router";
import Swal from "sweetalert2/dist/sweetalert2.js";
import * as Yup from "yup";

export default defineComponent({
  name: "sign-in",
  components: { Field, VForm, ErrorMessage },
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const submitButton = ref<HTMLButtonElement | null>(null);
    const login = Yup.object().shape({
      email: Yup.string().email().required().label("Email"),
      password: Yup.string().min(4).required().label("Password"),
    });
    const onSubmitLogin = async (values: any) => {
      values = values as User;
      store.logout();
      if (submitButton.value) { submitButton.value!.disabled = true; submitButton.value.setAttribute("data-kt-indicator", "on"); }
      await store.login(values);
      const error = Object.values(store.errors);
      if (error.length === 0) {
        Swal.fire({ text: "Login berhasil!", icon: "success", buttonsStyling: false, confirmButtonText: "Ok!", heightAuto: false, customClass: { confirmButton: "btn fw-semibold btn-light-primary" } })
          .then(() => { store.isAdmin() ? router.push({ name: "dashboard" }) : router.push({ name: "user-dashboard" }); });
      } else {
        Swal.fire({ text: error[0] as string, icon: "error", buttonsStyling: false, confirmButtonText: "Coba lagi!", heightAuto: false, customClass: { confirmButton: "btn fw-semibold btn-light-danger" } })
          .then(() => { store.errors = {}; });
      }
      submitButton.value?.removeAttribute("data-kt-indicator");
      submitButton.value!.disabled = false;
    };
    return { onSubmitLogin, login, submitButton, getAssetPath };
  },
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

.sign-in-container { font-family: 'DM Sans', sans-serif; }

.greeting-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(99,179,237,0.1); border: 1px solid rgba(99,179,237,0.25);
  color: #63b3ed; padding: 6px 14px; border-radius: 100px;
  font-size: 12px; font-weight: 500; letter-spacing: 0.3px;
}
.greeting-badge .dot {
  width: 6px; height: 6px; background: #63b3ed; border-radius: 50%;
  animation: pulseDot 2s infinite;
}
@keyframes pulseDot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }

.sign-in-title {
  font-family: 'Syne', sans-serif; font-size: 2.2rem; font-weight: 800;
  color: #f0f4ff; margin: 12px 0 8px; line-height: 1.15; letter-spacing: -0.5px;
}
.sign-in-subtitle { color: #8896b3; font-size: 14px; }
.accent-link { color: #63b3ed; font-weight: 600; text-decoration: none; transition: color 0.2s; }
.accent-link:hover { color: #90cdf4; }

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
  border-radius: 12px; padding: 14px 16px 14px 44px;
  color: #e2e8f0; font-size: 14px; font-family: 'DM Sans', sans-serif;
  transition: all 0.25s; outline: none;
}
.custom-input::placeholder { color: #2d4a6a; }
.custom-input:focus {
  border-color: #63b3ed; background: rgba(99,179,237,0.08);
  box-shadow: 0 0 0 4px rgba(99,179,237,0.1);
}
.error-text { color: #fc8181; font-size: 12px; margin-top: 6px; }

.submit-btn {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  background: linear-gradient(135deg, #2b6cb0 0%, #1C325E 100%);
  color: #fff; border: none; border-radius: 12px; padding: 15px 24px;
  font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif;
  cursor: pointer; transition: all 0.25s; position: relative; overflow: hidden;
}
.submit-btn::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, transparent 60%);
  pointer-events: none;
}
.submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(43,108,176,0.5); }
.submit-btn:active { transform: translateY(0); }
.submit-btn .indicator-label { display: flex; align-items: center; gap: 8px; }

.divider-wrapper { display: flex; align-items: center; gap: 12px; }
.divider-line { flex: 1; height: 1px; background: rgba(99,179,237,0.12); }
.divider-text { color: #2d4a6a; font-size: 12px; font-weight: 500; white-space: nowrap; }

.social-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.social-btn {
  display: flex; align-items: center; justify-content: center;
  background: rgba(49,130,206,0.06); border: 1.5px solid rgba(99,179,237,0.13);
  border-radius: 12px; padding: 12px 16px; color: #a0aec0;
  font-size: 13px; font-weight: 500; font-family: 'DM Sans', sans-serif;
  text-decoration: none; transition: all 0.2s;
}
.social-btn:hover {
  background: rgba(99,179,237,0.1); border-color: rgba(99,179,237,0.28);
  color: #e2e8f0; transform: translateY(-1px);
}
.social-btn-full { grid-column: span 2; }
</style>