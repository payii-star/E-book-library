<template>
  <!--begin::Wrapper-->
  <div class="w-lg-500px p-10">
    <!--begin::Form-->
    <VForm
      class="form w-100 fv-plugins-bootstrap5 fv-plugins-framework"
      @submit="onSubmitForgotPassword"
      id="kt_login_password_reset_form"
      :validation-schema="forgotPassword"
    >
      <!--begin::Heading-->
      <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-gray-900 mb-3">Forgot Password ?</h1>
        <!--end::Title-->

        <!--begin::Link-->
        <div class="text-gray-500 fw-semibold fs-4">
          Enter your email to reset your password.
        </div>
        <!--end::Link-->
      </div>
      <!--begin::Heading-->

      <!--begin::Input group-->
      <div class="fv-row mb-10">
        <label class="form-label fw-bold text-gray-900 fs-6">Email</label>
        <Field
          class="form-control form-control-solid"
          type="email"
          placeholder=""
          name="email"
          autocomplete="off"
        />
        <div class="fv-plugins-message-container">
          <div class="fv-help-block">
            <ErrorMessage name="email" />
          </div>
        </div>
      </div>
      <!--end::Input group-->

      <!--begin::Actions-->
      <div class="d-flex flex-wrap justify-content-center pb-lg-0">
        <button
          type="submit"
          ref="submitButton"
          id="kt_password_reset_submit"
          class="btn btn-lg btn-primary fw-bold me-4"
        >
          <span class="indicator-label"> Submit </span>
          <span class="indicator-progress">
            Please wait...
            <span
              class="spinner-border spinner-border-sm align-middle ms-2"
            ></span>
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
import { defineComponent, ref } from "vue";
import { ErrorMessage, Field, Form as VForm } from "vee-validate";
import * as Yup from "yup";
import * as axios from "axios";
import Swal from "sweetalert2/dist/sweetalert2.js";

export default defineComponent({
  name: "password-reset",
  components: {
    Field,
    VForm,
    ErrorMessage,
  },
  setup() {
    const submitButton = ref<HTMLButtonElement | null>(null);

    const forgotPassword = Yup.object().shape({
      email: Yup.string().email().required().label("Email"),
    });

    const onSubmitForgotPassword = async (values: any) => {
      submitButton.value!.disabled = true;
      submitButton.value?.setAttribute("data-kt-indicator", "on");

      try {
        await (axios as any).default.post(
          `${import.meta.env.VITE_APP_API_URL}/forgot-password`,
          { email: values.email }
        );

        Swal.fire({
          text: "Link reset password telah dikirim ke email kamu!",
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok, got it!",
          heightAuto: false,
          customClass: {
            confirmButton: "btn fw-semibold btn-light-primary",
          },
        });
      } catch (error: any) {
        const message =
          error.response?.data?.message || "Email tidak ditemukan.";

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
      onSubmitForgotPassword,
      forgotPassword,
      submitButton,
    };
  },
});
</script>