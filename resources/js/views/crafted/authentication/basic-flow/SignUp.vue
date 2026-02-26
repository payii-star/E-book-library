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
            <Field class="custom-input with-icon" type="text" placeholder="Nama lengkap kamu" name="name" autocomplete="off"/>
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
            <Field class="custom-input with-icon" type="email" placeholder="nama@email.com" name="email" autocomplete="off"/>
          </div>
          <div class="fv-plugins-message-container"><div class="fv-help-block error-text"><ErrorMessage name="email"/></div></div>
        </div>

        <!-- Password -->
        <div class="fv-row mb-5" data-kt-password-meter="true">
          <label class="input-label">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <Field class="custom-input with-icon" type="password" placeholder="Min. 8 karakter" name="password" autocomplete="off"/>
          </div>
          <div class="fv-plugins-message-container"><div class="fv-help-block error-text"><ErrorMessage name="password"/></div></div>
          <div class="d-flex gap-2 mt-3" data-kt-password-meter-control="highlight">
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px"></div>
            <div class="flex-grow-1 bg-secondary bg-active-success rounded h-4px"></div>
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
            <Field class="custom-input with-icon" type="password" placeholder="Ulangi password" name="password_confirmation" autocomplete="off"/>
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

        <!-- Submit -->
        <button id="kt_sign_up_submit" ref="submitButton" type="submit" class="submit-btn w-100">
          <span class="indicator-label">
            <span>Buat Akun</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </span>
          <span class="indicator-progress">
            <span class="spinner-border spinner-border-sm me-2"></span>Mohon tunggu...
          </span>
        </button>

      </VForm>
    </div>
  </div>
</template>

<script lang="ts">
import { getAssetPath } from "@/core/helpers/assets";
import { defineComponent, nextTick, onMounted, ref } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import { useAuthStore, type User } from "@/stores/auth";
import { useRouter } from "vue-router";
import { PasswordMeterComponent } from "@/assets/ts/components";
import Swal from "sweetalert2/dist/sweetalert2.js";

export default defineComponent({
  name: "sign-up",
  components: { Field, VForm, ErrorMessage },
  setup() {
    const store = useAuthStore();
    const router = useRouter();
    const submitButton = ref<HTMLButtonElement | null>(null);

    const registration = Yup.object().shape({
      name: Yup.string().required().label("Nama Lengkap"),
      email: Yup.string().min(4).required().email().label("Email"),
      password: Yup.string().min(8).required().label("Password"),
      password_confirmation: Yup.string()
        .required()
        .oneOf([Yup.ref("password")], "Password tidak cocok")
        .label("Konfirmasi Password"),
    });

    onMounted(() => { nextTick(() => { PasswordMeterComponent.bootstrap(); }); });

    const onSubmitRegister = async (values: any) => {
      values = values as User;
      store.logout();
      submitButton.value!.disabled = true;
      submitButton.value?.setAttribute("data-kt-indicator", "on");

      await store.register(values);
      const error = Object.values(store.errors);

      if (error.length === 0) {
        Swal.fire({
          text: "Akun berhasil dibuat!",
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok!",
          heightAuto: false,
          customClass: { confirmButton: "btn fw-semibold btn-light-primary" },
        }).then(() => { router.push({ name: "user-dashboard" }); });
      } else {
        Swal.fire({
          text: error[0] as string,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "Coba lagi!",
          heightAuto: false,
          customClass: { confirmButton: "btn fw-semibold btn-light-danger" },
        });
      }

      submitButton.value?.removeAttribute("data-kt-indicator");
      submitButton.value!.disabled = false;
    };

    return { registration, onSubmitRegister, submitButton, getAssetPath };
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
  border-radius: 12px; padding: 13px 16px;
  color: #e2e8f0; font-size: 14px; font-family: 'DM Sans', sans-serif;
  transition: all 0.25s; outline: none;
}
.custom-input.with-icon { padding-left: 44px; }
.custom-input::placeholder { color: #2d4a6a; }
.custom-input:focus {
  border-color: #63b3ed; background: rgba(99,179,237,0.08);
  box-shadow: 0 0 0 4px rgba(99,179,237,0.1);
}
.error-text { color: #fc8181; font-size: 12px; margin-top: 5px; }
.password-hint { color: #2d4a6a; font-size: 11.5px; }

.toc-wrapper { display: flex; align-items: flex-start; gap: 10px; cursor: pointer; }
.toc-checkbox { width: 16px; height: 16px; margin-top: 2px; accent-color: #63b3ed; flex-shrink: 0; }
.toc-text { color: #8896b3; font-size: 13px; line-height: 1.5; }

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
</style>