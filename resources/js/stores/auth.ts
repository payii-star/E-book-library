import { ref } from "vue";
import { defineStore } from "pinia";
import ApiService from "@/core/services/ApiService";
import JwtService from "@/core/services/JwtService";

export interface User {
  name: string;
  email: string;
  password?: string;
  api_token: string;
  role: string | number;
  phone?: string;
  avatar?: string;
  bio?: string;
  job_title?: string;
  company?: string;
}

export const useAuthStore = defineStore("auth", () => {
  const errors = ref<any>({});
  const user = ref<User>({} as User);
  const isAuthenticated = ref(!!JwtService.getToken());

  // ==============================
  // SET AUTH
  // ==============================
  function setAuth(authUser: User) {
    isAuthenticated.value = true;
    user.value = authUser;
    errors.value = {};
    JwtService.saveToken(authUser.api_token);
  }

  function setError(error: any) {
    errors.value = error || {};
  }

  function purgeAuth() {
    isAuthenticated.value = false;
    user.value = {} as User;
    errors.value = {};
    JwtService.destroyToken();
  }

  // ==============================
  // LOGIN
  // ==============================
  async function login(credentials: { email: string; password: string }) {
    try {
      const { data } = await ApiService.post("login", credentials);
      setAuth(data);
    } catch (error: any) {
      setError(error.response?.data?.errors);
      purgeAuth();
    }
  }

  async function adminLogin(credentials: { email: string; password: string }) {
    try {
      const { data } = await ApiService.post("admin/login", credentials);
      setAuth(data);
    } catch (error: any) {
      setError(error.response?.data?.errors);
      purgeAuth();
    }
  }

  function logout() {
    purgeAuth();
  }

  // ==============================
  // REGISTER
  // ==============================
  async function register(credentials: User) {
    try {
      const { data } = await ApiService.post("register", credentials);
      setAuth(data);
    } catch (error: any) {
      setError(error.response?.data?.errors);
    }
  }

  // ==============================
  // PROFILE UPDATE
  // ==============================
  async function updateProfile(profileData: Partial<User>) {
    try {
      const { data } = await ApiService.post("profile/update", profileData);
      user.value = data;
      errors.value = {};
    } catch (error: any) {
      setError(error.response?.data?.errors);
    }
  }

  async function uploadAvatar(file: File) {
    const formData = new FormData();
    formData.append("avatar", file);

    try {
      const { data } = await ApiService.post("profile/avatar", formData);
      user.value = data.user;
      errors.value = {};
      return data;
    } catch (error: any) {
      setError(error.response?.data?.errors);
      throw error;
    }
  }

  // ==============================
  // VERIFY TOKEN (FIX RACE ISSUE)
  // ==============================
  async function verifyAuth() {
    const token = JwtService.getToken();

    if (!token) {
      purgeAuth();
      return;
    }

    try {
      ApiService.setHeader();
      const { data } = await ApiService.post("verify_token", {
        api_token: token,
      });
      setAuth(data);
    } catch (error) {
      purgeAuth();
    }
  }

  // ==============================
  // ROLE CHECK (FLEXIBLE)
  // ==============================
  function isAdmin() {
    if (!user.value || !user.value.role) return false;

    const role = String(user.value.role).toLowerCase();

    return (
      role === "admin" ||
      role === "superadmin" ||
      role === "1"
    );
  }

  function isUser() {
    if (!user.value || !user.value.role) return false;

    const role = String(user.value.role).toLowerCase();

    return (
      role === "user" ||
      role === "2"
    );
  }

  return {
    errors,
    user,
    isAuthenticated,
    login,
    adminLogin,
    logout,
    register,
    updateProfile,
    uploadAvatar,
    verifyAuth,
    isAdmin,
    isUser,
  };
});