import { createRouter, createWebHashHistory } from "vue-router";
import HomeView from "../views/user/HomeView.vue";
import LatihanCSS from "@/views/Latihan.vue";

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  
  {
    path: "/latihan",
    name: "latihan",
    component: LatihanCSS
  },
  
];

const router = createRouter({
  history: createWebHashHistory(),
  routes,
});

export default router;
