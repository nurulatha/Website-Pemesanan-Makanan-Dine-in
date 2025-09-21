// main.js
import { createApp } from "vue";
import { createWebHistory, createRouter } from "vue-router";
import DataTable from "datatables.net-vue3";
import $ from "jquery";
import Toastify, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

// styles
import "@fortawesome/fontawesome-free/css/all.min.css";
import "@/assets/styles/tailwind.css";
import "@/assets/styles/dataTable.css";
import "v-calendar/style.css";

// mouting point for the whole app
import App from "@/App.vue";

// layouts
import Admin from "@/layouts/Admin.vue";
import Auth from "@/layouts/Auth.vue";

// views for Admin layout
import Dashboard from "@/views/admin/Dashboard.vue";
import Settings from "@/views/admin/Settings.vue";
import Menus from "@/views/admin/Menus.vue";
import Status from "@/views/admin/Status.vue";
import ReportMenu from "@/views/admin/ReportMenu.vue";
import ReportTable from "@/views/admin/ReportTable.vue";

// views for Auth layout
import Login from "@/views/auth/Login.vue";
import Register from "@/views/auth/Register.vue";

// views without layouts
import Landing from "@/views/Landing.vue";
import Profile from "@/views/Profile.vue";
import DetailOrder from "@/components/DetailOrder.vue";

// routes
const routes = [
  {
    path: "/admin",
    redirect: "/admin/dashboard",
    component: Admin,
    children: [
      {
        path: "/admin/dashboard",
        component: Dashboard,
      },
      {
        path: "/admin/settings",
        component: Settings,
      },
      {
        path: "/admin/menus",
        component: Menus,
      },
      {
        path: "/admin/status",
        component: Status,
      },
      {
        path: "/admin/report",
        children: [
          {
            path: "/admin/report/menu",
            component: ReportMenu,
          },
          {
            path: "/admin/report/table",
            component: ReportTable,
          },
        ],
      },
      {
        path: "/admin/table/:tableId",
        name: "DetailOrder",
        component: DetailOrder,
      },
    ],
  },
  {
    path: "/",
    redirect: "/auth/login",
    component: Auth,
    children: [
      {
        path: "/auth/login",
        component: Login,
      },
      {
        path: "/auth/register",
        component: Register,
      },
    ],
  },
  {
    path: "/landing",
    component: Landing,
  },
  {
    path: "/profile",
    component: Profile,
  },
  { path: "/:pathMatch(.*)*", redirect: "/" },
];

// Buat app setelah import semua
const app = createApp(App);

// Setup jQuery dan DataTable
app.config.globalProperties.$toast = toast;
app.config.globalProperties.$ = $;
app.use(DataTable);

// Setup VCalendar
app.use(Toastify, {
  position: "top-right",
  autoClose: 1000,
});

// Setup global API URL
app.config.globalProperties.$apiURL = "https://aa6a-140-213-59-146.ngrok-free.app";
app.config.globalProperties.$URLQR = "http://192.168.220.206:8080/orders/";


// Setup routing
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Mount the app
app.use(router).mount("#app");
