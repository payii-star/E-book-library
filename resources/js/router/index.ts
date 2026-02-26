import {
  createRouter,
  createWebHistory,
  type RouteRecordRaw,
} from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { useConfigStore } from "@/stores/config";

const routes: Array<RouteRecordRaw> = [
  // ===============================
  // ===== ADMIN ROUTES ============
  // ===============================
  {
    path: "/",
    redirect: "/dashboard",
    component: () => import("@/layouts/default-layout/DefaultLayout.vue"),
    meta: { middleware: "auth" },
    children: [
      {
        path: "/dashboard",
        name: "dashboard",
        component: () => import("@/views/Dashboard.vue"),
        meta: { pageTitle: "Dashboard", breadcrumbs: ["Admin"] },
      },
      {
        path: "/admin/books",
        name: "admin-books",
        component: () => import("@/views/BookList.vue"),
        meta: { pageTitle: "Manajemen Buku", breadcrumbs: ["Perpustakaan"] },
      },
      {
        path: "/admin/books/create",
        name: "admin-books-create",
        component: () => import("@/views/BookForm.vue"),
        meta: { pageTitle: "Tambah Buku", breadcrumbs: ["Perpustakaan", "Buku"] },
      },
      {
        path: "/admin/books/:id/edit",
        name: "admin-books-edit",
        component: () => import("@/views/BookForm.vue"),
        meta: { pageTitle: "Edit Buku", breadcrumbs: ["Perpustakaan", "Buku"] },
      },
      {
        path: "/admin/books/:id/chapters",
        name: "admin-book-chapters",
        component: () => import("@/views/ChapterManager.vue"),
        meta: { pageTitle: "Kelola Chapter", breadcrumbs: ["Perpustakaan", "Chapter"] },
      },
      {
        path: "/admin/chapters",
        name: "admin-chapters",
        component: () => import("@/views/ChapterManager.vue"),
        meta: { pageTitle: "Kelola Chapter", breadcrumbs: ["Perpustakaan", "Chapter"] },
      },
      {
        path: "/admin/categories",
        name: "admin-categories",
        component: () => import("@/views/CategoryList.vue"),
        meta: { pageTitle: "Kategori Buku", breadcrumbs: ["Perpustakaan"] },
      },
      {
        path: "/admin/users",
        name: "admin-users",
        component: () => import("@/views/UserList.vue"),
        meta: { pageTitle: "Data Anggota", breadcrumbs: ["Perpustakaan"] },
      },
      {
        path: "/admin/reports",
        name: "admin-reports",
        component: () => import("@/views/Reports.vue"),
        meta: { pageTitle: "Laporan Lengkap", breadcrumbs: ["Admin"] },
      },
    ],
  },

  // ===============================
  // ===== USER ROUTES ============
  // ===============================
  {
    path: "/user",
    redirect: "/user/dashboard",
    component: () => import("@/layouts/UserLayout.vue"),
    meta: { middleware: "auth-user" },
    children: [
      {
        path: "/user/dashboard",
        name: "user-dashboard",
        component: () => import("@/views/user/UserDashboard.vue"),
        meta: { pageTitle: "Dashboard", middleware: "auth-user" },
      },
      {
        path: "/user/profile",
        name: "user-profile",
        component: () => import("@/views/user/UserProfile.vue"),
        meta: { pageTitle: "My Profile", middleware: "auth-user" },
      },
      {
        path: "/user/books",
        name: "user-books",
        component: () => import("@/views/user/BookList.vue"),
        meta: { pageTitle: "Perpustakaan", middleware: "auth-user" },
      },
      {
        path: "/user/books/:slug",
        name: "user-book-detail",
        component: () => import("@/views/user/BookDetail.vue"),
        meta: { pageTitle: "Detail Buku", middleware: "auth-user" },
      },
      {
        path: "/user/books/:slug/read/:number",
        name: "user-read-chapter",
        component: () => import("@/views/user/ReadChapter.vue"),
        meta: { pageTitle: "Baca Buku", middleware: "auth-user" },
      },
    ],
  },

  // ===============================
  // ===== AUTH ROUTES ============
  // ===============================
  {
    path: "/",
    component: () => import("@/layouts/AuthLayout.vue"),
    children: [
      {
        path: "/sign-in",
        name: "sign-in",
        component: () => import("@/views/crafted/authentication/basic-flow/SignIn.vue"),
        meta: { pageTitle: "Sign In" },
      },
      {
        path: "/sign-up",
        name: "sign-up",
        component: () => import("@/views/crafted/authentication/basic-flow/SignUp.vue"),
        meta: { pageTitle: "Sign Up" },
      },
      {
        path: "/password-reset",
        name: "password-reset",
        component: () => import("@/views/crafted/authentication/basic-flow/PasswordReset.vue"),
        meta: { pageTitle: "Lupa Password" },
      },
      {
        path: "/user/sign-in",
        name: "user-sign-in",
        component: () => import("@/views/user/UserSignIn.vue"),
        meta: { pageTitle: "User Sign In" },
      },
    ],
  },

  // ===============================
  // ===== ERROR ROUTES ===========
  // ===============================
  {
    path: "/404",
    name: "404",
    component: () => import("@/views/crafted/authentication/Error404.vue"),
    meta: { pageTitle: "Error 404" },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/404",
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  const configStore = useConfigStore();

  document.title = `${to.meta.pageTitle || "App"} - ${import.meta.env.VITE_APP_NAME}`;
  configStore.resetLayoutConfig();

  // ✅ await verifyAuth agar token sempat divalidasi sebelum guard
  await authStore.verifyAuth();

  if (to.meta.middleware === "auth") {
    if (authStore.isAuthenticated && authStore.isAdmin()) {
      next();
    } else if (authStore.isAuthenticated && authStore.isUser()) {
      next({ name: "user-dashboard" });
    } else {
      next({ name: "sign-in" });
    }
  } else if (to.meta.middleware === "auth-user") {
    if (authStore.isAuthenticated && authStore.isUser()) {
      next();
    } else if (authStore.isAuthenticated && authStore.isAdmin()) {
      next({ name: "dashboard" });
    } else {
      next({ name: "user-sign-in" });
    }
  } else {
    next();
  }
});

export default router;