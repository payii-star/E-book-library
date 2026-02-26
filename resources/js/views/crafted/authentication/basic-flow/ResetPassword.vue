<template>
  <!--begin::Wrapper-->
  <div class="w-lg-500px p-10">
    <!--begin::Form-->
    <VForm
      class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework"
      @submit="onSubmitResetPassword"
      id="kt_login_reset_password_form"
      :validation-schema="resetPassword"
    >
      <!--begin::Heading-->
      <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-gray-900 mb-3">Reset Password</h1>
        <!--end::Title-->

        <!--begin::Link-->
        <div class="text-gray-500 fw-semibold fs-4">
          Enter your new password below.
        </div>
        <!--end::Link-->
      </div>
      <!--end::Heading-->

      <!--begin::Input group - Password-->
      <div class="fv-row mb-10">
        <label class="form-label fw-bold text-gray-900 fs-6">New Password</label>
        <Field
          class="form-control form-control-solid"
          type="password"
          placeholder=""
          name="password"
          autocomplete="off"
        />
        <div class="fv-plugins-message-container">
          <div class="fv-help-block">
            <ErrorMessage name="password" />
          </div>
        </div>
      </div>
      <!--end::Input group-->

      <!--begin::Input group - Confirm Password-->
      <div class="fv-row mb-10">
        <label class="form-label fw-bold text-gray-900 fs-6">Confirm Password</label>
        <Field
          class="form-control form-control-solid"
          type="password"
          placeholder=""
          name="password_confirmation"
          autocomplete="off"
        />
        <div class="fv-plugins-message-container">
          <div class="fv-help-block">
            <ErrorMessage name="password_confirmation" />
          </div>
        </div>
      </div>
      <!--end::Input group-->

      <!--begin::Actions-->
      <div class="d-flex flex-wrap justify-content-center pb-lg-0">
        <button
          type="submit"
          ref="submitButton"
          id="kt_reset_password_submit"
          class="btn btn-lg btn-primary fw-bold me-4"
        >
          <span class="indicator-label"> Submit </span>
          <span class="indicator-progress">
            Please wait...
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
          </span>
        </button>

        <router-link to="/sign-in" class="btn btn-lg btn-light-primary fw-bold"
          >Cancel</router-link
        >
      </div>
      <!--end::Actions-->
    </VForm>
    <!--end::Form-->
  </div>
  <!--end::Wrapper-->
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import { useRouter, useRoute } from "vue-router";
import * as Yup from "yup";
import Swal from "sweetalert2/dist/sweetalert2.js";
import axios from "axios";

export default defineComponent({
  name: "reset-password",
  components: {
    Field,
    VForm,
    ErrorMessage,
  },
  setup() {
    const router = useRouter();
    const route = useRoute();
    const submitButton = ref<HTMLButtonElement | null>(null);

    const token = ref("");
    const email = ref("");

    // Ambil token & email dari URL otomatis saat halaman dibuka
    onMounted(() => {
      token.value = route.query.token as string;
      email.value = route.query.email as string;
    });

    // Validasi form
    const resetPassword = Yup.object().shape({
      password: Yup.string().min(8).required().label("Password"),
      password_confirmation: Yup.string()
        .oneOf([Yup.ref("password")], "Password tidak cocok")
        .required()
        .label("Confirm Password"),
    });

    // Submit form
    const onSubmitResetPassword = async (values: any) => {
      submitButton.value!.disabled = true;
      submitButton.value?.setAttribute("data-kt-indicator", "on");

      try {
        await axios.post(`${import.meta.env.VITE_APP_API_URL}/reset-password`, {
          token: token.value,
          email: email.value,
          password: values.password,
          password_confirmation: values.password_confirmation,
        });

        Swal.fire({
          text: "Password berhasil direset! Silakan login kembali.",
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok, got it!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-primary",
          },
        }).then(() => {
          router.push("/sign-in");
        });
      } catch (error: any) {
        const message =
          error.response?.data?.message || "Terjadi kesalahan, coba lagi.";

        Swal.fire({
          text: message,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "Try again!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-danger",
          },
        });
      }

      submitButton.value?.removeAttribute("data-kt-indicator");
      submitButton.value!.disabled = false;
    };

    return {
      onSubmitResetPassword,
      resetPassword,
      submitButton,
    };
  },
});
</script>