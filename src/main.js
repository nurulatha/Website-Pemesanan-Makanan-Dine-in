import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import Toastify, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import VueScreen from "vue-screen";

import "bootstrap/dist/css/bootstrap.css";
import "bootstrap/dist/js/bootstrap.bundle";
import "@fortawesome/fontawesome-free/css/all.css";
import "./assets/css/main.css";
import "./assets/css/tailwind.css";
import DataTable from "datatables.net-vue3";
import VCalendar from "v-calendar";

const app = createApp(App);

app.use(VCalendar, {
  componentPrefix: "vc", // Opsi prefix komponen (opsional)
});

app.use(Toastify, {
  position: "top-right",
  autoClose: 2500,
});
app.use(DataTable);
app.use(VueScreen, "bootstrap");
app.config.globalProperties.$toast = toast;
app.config.globalProperties.$apiURL = "https://aa6a-140-213-59-146.ngrok-free.app";
app.use(router).mount("#app");
