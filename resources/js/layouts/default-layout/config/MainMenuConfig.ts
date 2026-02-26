const MainMenuConfig = [
  {
    pages: [
      // =========================
      // DASHBOARD
      // =========================
      {
        heading: "Dashboard",
        route: "/dashboard",
        svgIcon: "media/icons/duotune/general/gen025.svg",
        fontIcon: "bi-speedometer2",
      },

      // =========================
      // PERPUSTAKAAN
      // =========================
      {
        sectionTitle: "Perpustakaan",
        route: "/admin/books",
        svgIcon: "media/icons/duotune/files/fil020.svg",
        fontIcon: "bi-book",
        sub: [
          {
            heading: "Manajemen Buku",
            route: "/admin/books",
          },
          {
            heading: "Tambah Buku",
            route: "/admin/books/create",
          },
          {
            heading: "Kategori",
            route: "/admin/categories",
          },
        ],
      },

      // =========================
      // USER MANAGEMENT
      // =========================
      {
        sectionTitle: "Manajemen User",
        route: "/admin/users",
        svgIcon: "media/icons/duotune/communication/com013.svg",
        fontIcon: "bi-people",
        sub: [
          {
            heading: "Data User",
            route: "/admin/users",
          },
        ],
      },
    ],
  },
];

export default MainMenuConfig;