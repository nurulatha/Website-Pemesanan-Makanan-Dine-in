<template>
  <div>
    <sidebar />
    <div class="relative md:ml-64 bg-blueGray-100">
      <admin-navbar />
      <!-- Gunakan flex dan tambahkan margin-top pada router-view -->
      <div class="flex flex-col px-4 md:px-10 mx-auto w-full -m-20">
        <router-view class="mt-4" />
        <footer-admin  />
      </div>
    </div>
  </div>
</template>

<script>
import AdminNavbar from "@/components/Navbars/AdminNavbar.vue";
import Sidebar from "@/components/Sidebar/Sidebar.vue";
import FooterAdmin from "@/components/Footers/FooterAdmin.vue";
export default {
  name: "admin-layout",
  components: {
    AdminNavbar,
    Sidebar,
    FooterAdmin,
  },
};
</script>
