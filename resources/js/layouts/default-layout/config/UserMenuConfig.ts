import type { MenuItem } from "@/layouts/default-layout/config/types";

const UserMenuConfig: Array<MenuItem> = [
  {
    pages: [
      {
        heading: "dashboard",
        route: "/user/dashboard",
        keenthemesIcon: "element-11",
        bootstrapIcon: "bi-app-indicator",
      },
    ],
  },
  {
    heading: "Perpustakaan",
    route: "/user",
    pages: [
      {
        heading: "Jelajahi Buku",
        route: "/user/books",
        keenthemesIcon: "book-open",
        bootstrapIcon: "bi-book",
      },
    ],
  },
  {
    heading: "account",
    route: "/user",
    pages: [
      {
        heading: "My Profile",
        route: "/user/profile",
        keenthemesIcon: "profile-circle",
        bootstrapIcon: "bi-person",
      },
    ],
  },
];

export default UserMenuConfig;